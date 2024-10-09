<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import NewPrompt from './Partials/NewPrompt.vue';

defineProps({
    conversations: {
        type: Array,
    },
});
</script>

<template>
    <Head title="My Conversations" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Conversations</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <h4 class="font-semibold text-gray-800 leading-tight mb-3">Start a New Conversation:</h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <NewPrompt class="max-w-xl" />
                    </div>
                    <h4 class="font-semibold text-gray-800 leading-tight mb-3">Or click on any past conversation to continue from where you stopped:</h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <template v-for="conversation in conversations">
                            <a class="p-4 bg-gray-100 rounded-lg" :href="route('prompt.continue', {'prompt_chain_id' : conversation.prompt_chain_id})">
                                <h3 class="text-lg font-semibold text-gray-800">{{ conversation.message }}</h3>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
