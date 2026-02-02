<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { ExclamationCircleIcon, EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid';

const router = useRouter();

let form = reactive({
    email: '',
    password: '',
});

let error = ref('');
let loading = ref(false);
let showPassword = ref(false);

const login = async () => {
    loading.value = true;
    error.value = '';

    try {
        let response = await axios.post('/api/login_process', form);

        if (response.data.success) {
            localStorage.setItem('token', response.data.data.token);
            router.push('/create_pr');
        } else {
            error.value = response.data.message;
        }
    } catch (err) {
        error.value = "An error occurred. Please try again.";
    } finally {
        loading.value = false;
    }
};
</script>

<template>
<div class="min-h-screen flex bg-gradient-to-br from-blue-950 to-blue-900">
	<div
		class="absolute inset-0 pt-20 overflow-hidden animated-bg
			bg-[radial-gradient(circle,rgba(255,255,255,0.15)_1px,transparent_1px)]
			bg-[size:24px_24px]">
	</div>
    <!-- Left Branding Panel -->
    <div class="hidden lg:flex w-1/2 items-center justify-center text-white p-12">
        <div class="max-w-md text-center">
            <h1 class="text-4xl font-bold mb-4">Welcome Back</h1>
            <p class="text-white/80 text-lg">
                Sign in to securely access your purchasing and request system.
            </p>
        </div>
    </div>

    <!-- Right Login Panel -->
    <div class="flex w-full lg:w-1/2 items-center justify-center p-6">
        <div class="w-full max-w-md">

            <div class="bg-white backdrop-blur rounded-2xl shadow-2xl p-8">

                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-800">Sign In</h2>
                    <p class="text-gray-500 text-sm mt-2">
                        Enter your account credentials
                    </p>
                </div>

                <!-- Error Alert -->
                <div v-if="error"
                     class="flex items-start gap-3 p-4 mb-6 text-sm text-red-800 bg-red-50 border border-red-200 rounded-xl">
                    <ExclamationCircleIcon class="w-5 h-5 mt-0.5" />
                    <span>{{ error }}</span>
                </div>

                <!-- Form -->
                <form @submit.prevent="login" class="space-y-5">

                    <!-- Email -->
                    <div>
                        <label class="text-sm font-semibold text-gray-600">
                            Email Address
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            placeholder="you@example.com"
                            class="w-full mt-2 px-4 py-3 rounded-xl border border-gray-300
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                   outline-none transition"
                        />
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="text-sm font-semibold text-gray-600">
                            Password
                        </label>

                        <div class="relative mt-2">
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                placeholder="Enter your password"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                       outline-none transition pr-12"
                            />

                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-4 flex items-center text-gray-500 hover:text-gray-700"
                            >
                                <EyeSlashIcon v-if="showPassword" class="w-5 h-5"/>
                                <EyeIcon v-else class="w-5 h-5"/>
                            </button>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full flex items-center justify-center gap-2
                               bg-blue-600 hover:bg-blue-700
                               disabled:bg-blue-400
                               text-white font-semibold py-3 rounded-xl
                               transition shadow-md"
                    >
                        <svg v-if="loading"
                             class="animate-spin h-5 w-5"
                             viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                    fill="none"
                                    stroke-linecap="round"/>
                        </svg>

                        <span>{{ loading ? 'Signing in...' : 'Login' }}</span>
                    </button>

                </form>

            </div>

            <!-- Footer -->
            <p class="text-center text-white/80 text-sm mt-6">
                © {{ new Date().getFullYear() }} CENPRI
            </p>

        </div>
    </div>

</div>
</template>
