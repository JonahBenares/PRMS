<script setup>
import { reactive, ref, onMounted } from "vue";
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
    loading.value = true;    // start loading
    error.value = '';         // clear previous errors

    try {
        let response = await axios.post('/api/login_process', form);

        if (response.data.success) {
            localStorage.setItem('token', response.data.data.token); // save token
            router.push('/create_pr'); // redirect
        } else {
            error.value = response.data.message; // show API error
        }
    } catch (err) {
        error.value = "An error occurred. Please try again."; // catch network/server errors
    } finally {
        loading.value = false; // stop loading
    }
};
</script>

<template>
	<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
		<div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
			<!-- Logo / Title -->
			<div class="text-center mb-6">
				<h1 class="text-2xl font-bold text-gray-800">Login</h1>
				<p class="text-sm text-gray-500">Enter your credentials to access your account</p>
			</div>
			<hr class="my-4">
			<div v-if="error" class="flex items-center p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                <ExclamationCircleIcon class="w-5 h-5 mr-2" />
                <span>{{ error }}</span>
            </div>
			<!-- Login Form -->
			<form @submit.prevent="login" class="space-y-4">
				<!-- Email -->
				<div>
				<label for="email" class="block text-sm font-medium text-gray-700">Email</label>
				<input
					id="email"
					v-model="form.email"
					type="email"
					required
					class="mt-1 block w-full rounded-lg border px-3 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none"
				/>
				</div>

				<!-- Password -->
				<div>
					<label for="password" class="block text-sm font-medium text-gray-700">Password</label>
					<div class="relative">
						<input
							id="password"
							v-model="form.password"
							:type="showPassword ? 'text' : 'password'"
							required
							class="mt-1 block w-full rounded-lg border px-3 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none"
						/>

						<button
							type="button"
							@click="showPassword = !showPassword"
							class="absolute inset-y-0 right-3 flex items-center text-gray-500"
						>
							<EyeSlashIcon v-if="showPassword" class="w-5 h-5" />
							<EyeIcon v-else class="w-5 h-5" />
						</button>
					</div>
				</div>

				<!-- Remember + Forgot -->
				<!-- <div class="flex items-center justify-between">
				<label class="flex items-center space-x-2 text-sm text-gray-600">
					<input type="checkbox" v-model="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
					<span>Remember me</span>
				</label>
				<a href="/forgot-password" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
				</div> -->

				<!-- Submit Button -->
				<button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-300 mt-2 mt-3" :disabled="loading">
                    <span v-if="loading">Logging in...</span>
                    <span v-else>Login</span>
                </button>
			</form>

			<!-- Divider -->
			<!-- <div class="flex items-center my-6">
				<div class="flex-grow border-t border-gray-300"></div>
				<span class="mx-3 text-sm text-gray-500">or</span>
				<div class="flex-grow border-t border-gray-300"></div>
			</div> -->

			<!-- Register -->
			<!-- <p class="text-center text-sm text-gray-600">
				Don’t have an account?
				<a href="/register" class="text-blue-600 font-medium hover:underline">Register</a>
			</p> -->
		</div>
	</div>
</template>
