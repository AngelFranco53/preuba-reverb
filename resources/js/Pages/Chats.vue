<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from "axios";
import { nextTick, onMounted, ref, watch } from "vue";

const props = defineProps({
    currentUser: {
        type: Object,
        required: true,
    },
    friend: {
        type: Object,
        required: true,
    },
});

const messages = ref([]);
const newMessage = ref("");
const messagesContainer = ref(null);
const isFriendTyping = ref(false);
const isFriendTypingTimer = ref(null);
 
watch(
    messages,
    () => {
        nextTick(() => {
            messagesContainer.value.scrollTo({
                top: messagesContainer.value.scrollHeight,
                behavior: "smooth",
            });
        });
    },
    { deep: true }
);
 
const sendMessage = () => {
    if (newMessage.value.trim() !== "") {
        axios
            .post(`/messages/${props.friend.id}`, {
                message: newMessage.value,
            })
            .then((response) => {
                messages.value.push(response.data);
                newMessage.value = "";
            });
    }
};
 
const sendTypingEvent = () => {
    Echo.private(`chat.${props.friend.id}`).whisper("typing", {
        userID: props.currentUser.id,
    });
};
 
onMounted(() => {
    axios.get(`/messages/${props.friend.id}`).then((response) => {
        console.log(response.data);
        messages.value = response.data;
    });
 
    Echo.private(`chat.${props.currentUser.id}`)
        .listen("MessageSent", (response) => {
            messages.value.push(response.message);
        })
        .listenForWhisper("typing", (response) => {
            isFriendTyping.value = response.userID === props.friend.id;
 
            if (isFriendTypingTimer.value) {
                clearTimeout(isFriendTypingTimer.value);
            }
 
            isFriendTypingTimer.value = setTimeout(() => {
                isFriendTyping.value = false;
            }, 1000);
        });
});
</script>

<template>
    <Head title="chats" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Chats with {{ props.friend.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex flex-col justify-end h-80">
                            <div ref="messagesContainer" class="p-4 overflow-y-auto max-h-fit">
                                <div
                                    v-for="message in messages"
                                    :key="message.id"
                                    class="flex items-center mb-2"
                                >
                                    <div
                                        v-if="message.sender_id === currentUser.id"
                                        class="p-2 ml-auto text-white bg-blue-500 rounded-lg"
                                    >
                                        {{ message.text }}
                                    </div>
                                    <div v-else class="p-2 mr-auto bg-gray-200 rounded-lg text-zinc-600">
                                        {{ message.text }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <input
                                type="text"
                                v-model="newMessage"
                                @keydown="sendTypingEvent"
                                @keyup.enter="sendMessage"
                                placeholder="Type a message..."
                                class="flex-1 px-2 py-1 border rounded-lg text-black"
                            />
                            <button
                                @click="sendMessage"
                                class="px-4 py-1 ml-2 text-white bg-blue-500 rounded-lg"
                            >
                                Send
                            </button>
                        </div>
                        <small v-if="isFriendTyping" class="text-gray-700">
                            {{ friend.name }} is typing...
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>