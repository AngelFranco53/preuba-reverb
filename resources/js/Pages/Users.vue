<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const goToChat = (userId) => {
    console.log(userId);
    
    route('chat', userId);
}; 
</script>

<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Chats
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        users
                        <div
                            class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-3"
                        >
                            <div
                                v-for="user in users"
                                :key="user.id"
                                class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800 shadow-red-500"
                                @click="goToChat(user)"
                            >
                                <a :href="route('chat', user.id)">
                                    

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <img
                                                src="https://img.icons8.com/fluency/48/user-male-circle--v1.png"
                                                alt="avatar"
                                                class="w-10 h-10 rounded-full"
                                            />
                                            <div class="ml-2">
                                                <span class="text-lg font-semibold text-gray-800 dark:text-gray-200"
                                                    >{{ user.name }}
                                                </span>
                                                <span class="block text-sm font-medium text-gray-500 dark:text-gray-400"
                                                    >{{ user.email }}
                                                </span>
                                            </div>    
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>