<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm, usePage} from '@inertiajs/vue3';
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {ref} from "vue";

const promptInput        = ref(null);

const form = useForm({
    prompt_input: '',
});

defineProps({
    messages: {
        type: Array,
    },
    promptChainId: {
        type: String,
    },
});

const promptChainId = usePage().props.promptChainId;
const newMessage = () => {
    form.put(route('prompt.new-message', {'prompt_chain_id' : promptChainId}), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            if (form.errors.prompt_input) {
                form.reset('prompt_input');
                prompt_input.value.focus();
            }
        },
    });
};

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
                    <div class="grid grid-cols-1 gap-4">
                        <template v-for="message in messages">
                            <div class="p-4 rounded-lg" :class="{'bg-gray-100' : message.role === 'user', 'bg-slate-300' : message.role !== 'user' }" >
                                <h3 class="text-lg font-semibold text-gray-800">{{ message.message }}</h3>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <form class="m-6 space-y-6" @submit.prevent="newMessage">
                        <div>
                            <InputLabel for="prompt_input" value="Type your message" />

                            <TextInput
                                id="prompt_input"
                                ref="promptInput"
                                v-model="form.prompt_input"
                                autocomplete="no"
                                class="mt-1 block w-full"
                                type="text"
                            />
                            <InputError :message="form.errors.prompt_input" class="mt-2" />
                        </div>
                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing" class="ms-auto">Ask me</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
