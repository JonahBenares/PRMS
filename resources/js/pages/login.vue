<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
      <!-- Logo / Title -->
      <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Login</h1>
        <p class="text-sm text-gray-500">Enter your credentials to access your account</p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="space-y-4">
        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            class="mt-1 block w-full rounded-lg border px-3 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none"
          />
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            class="mt-1 block w-full rounded-lg border px-3 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none"
          />
        </div>

        <!-- Remember + Forgot -->
        <div class="flex items-center justify-between">
          <label class="flex items-center space-x-2 text-sm text-gray-600">
            <input type="checkbox" v-model="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
            <span>Remember me</span>
          </label>
          <a href="/forgot-password" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="w-full py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 focus:ring-2 focus:ring-blue-500"
        >
          Log In
        </button>
      </form>

      <!-- Divider -->
      <div class="flex items-center my-6">
        <div class="flex-grow border-t border-gray-300"></div>
        <span class="mx-3 text-sm text-gray-500">or</span>
        <div class="flex-grow border-t border-gray-300"></div>
      </div>

      <!-- Register -->
      <p class="text-center text-sm text-gray-600">
        Don’t have an account?
        <a href="/register" class="text-blue-600 font-medium hover:underline">Register</a>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const email = ref("");
const password = ref("");
const remember = ref(false);

const handleLogin = () => {
  // Mock role check
  let role = "user"; // default role

  if (email.value === "admin@example.com") {
    role = "admin";
  }

  console.log("Mock login:", {
    email: email.value,
    password: password.value,
    remember: remember.value,
    role,
  });

  // Redirect based on role
  if (role === "admin") {
    router.push("/admin/dashboard");
  } else {
    router.push("/user/dashboard");
  }
};
</script>
