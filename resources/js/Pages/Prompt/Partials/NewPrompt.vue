<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';


const promptInput        = ref(null);

const form = useForm({
    prompt_input: '',
});

const startPrompt = () => {
    form.post(route('prompt.start'), {
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
    <section>
        <header class="m-6" >
            <h2 class="text-lg font-medium text-gray-900">Ask me anything:</h2>
        </header>

        <form class="m-6 space-y-6" @submit.prevent="startPrompt">
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
    </section>
</template>
