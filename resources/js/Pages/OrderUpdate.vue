<template>
    <Head title="Orders" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Orders {{ order.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form 
                            action=""
                            method="post"
                            @submit.prevent="updateOrder"
                        >
                            <select 
                                name="status" 
                                id="status"
                                v-model="order.status"
                                class="text-gray-900"
                            >
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                            </select>

                            <button
                                type="submit"
                                class="px-4 py-2 text-white bg-indigo-600 rounded-md"
                            >
                                Update
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';


const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});



const updateOrder = () => {
    axios.post(`/orders/${props.order.id}`, {
        order: props.order,
    }).then(() => {
        // Redirect to the orders page
        window.location.href = route('orders');
    }).catch(error => {
        console.error('Error updating order:', error);
    });
};
</script>