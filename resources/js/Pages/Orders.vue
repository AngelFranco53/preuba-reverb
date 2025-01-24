<template>
    <Head title="Orders" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Orders
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <table class="w-full table-auto">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">ID</th>
                                    <th class="px-4 py-2">Title</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in orders" :key="order.id">
                                    <td class="border px-4 py-2">{{ order.id }}</td>
                                    <td class="border px-4 py-2">{{ order.title }}</td>
                                    <td class="border px-4 py-2">{{ order.status }}</td>
                                    <td class="border px-4 py-2">
                                        <a
                                            :href="route('order.update', order.id)"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            Update
                                        </a>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';


const orderizer = ref([]);

const orders = computed( () => {
    return orderizer.value;
})

onMounted(() => {
    axios.get('/orders-get').then((response) => {
        orderizer.value = response.data;
    });
    Echo.private('order')
        .listen('UpdateOrder', () => {
            axios.get('/api/orders-get').then((response) => {
                orderizer.value = response.data;
            });
        })
});
</script>