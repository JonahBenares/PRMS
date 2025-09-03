<template>
  <div class="text-gray-800 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="fixed top-0 w-full z-50 bg-white backdrop-blur-md text-gray-700 shadow-sm">
		<div class="max-w-7xl mx-auto px-8 py-4 flex justify-between items-center">
			<!-- Logo -->
			<div class="text-xl font-bold">PRMS</div>

			<!-- Desktop Menu (with Profile inside) -->
			<nav class="flex items-center space-x-6 hidden md:flex">
			<a href="#" class="hover:text-blue-500">PR List</a>
			<!-- <a href="#" class="hover:text-blue-500">About</a>
			<a href="#" class="hover:text-blue-500">Services</a>
			<a href="#" class="hover:text-blue-500">Contact</a> -->

			<!-- Profile Dropdown -->
			<div class="relative">
				<button @click="profileOpen = !profileOpen" class="flex items-center space-x-2 focus:outline-none">
				<img src="https://i.pravatar.cc/40" alt="Profile" class="w-8 h-8 rounded-full">
				<span class="font-medium">John Doe</span>
				<svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
					viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
				</svg>
				</button>

				<!-- Dropdown -->
				<transition name="fade">
				<div
					v-if="profileOpen"
					class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg overflow-hidden"
				>
					<a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Settings</a>
					<a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Change Password</a>
					<a href="/" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Logout</a>
				</div>
				</transition>
			</div>
			</nav>

			<!-- Mobile Toggle Button -->
			<button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden">
			<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<!-- Hamburger -->
				<path v-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
				d="M4 6h16M4 12h16M4 18h16" />
				<!-- Close (X) -->
				<path v-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
				d="M6 18L18 6M6 6l12 12" />
			</svg>
			</button>
		</div>

      <!-- Mobile Menu -->
      <transition name="fade">
        <div v-if="mobileMenuOpen" class="md:hidden bg-white/90 backdrop-blur-lg px-4 pb-4">
          <nav class="flex flex-col space-y-2">
            <a href="#" class="block py-2 text-gray-700 hover:text-blue-500">Home</a>
            <a href="#" class="block py-2 text-gray-700 hover:text-blue-500">About</a>
            <a href="#" class="block py-2 text-gray-700 hover:text-blue-500">Services</a>
            <a href="#" class="block py-2 text-gray-700 hover:text-blue-500">Contact</a>
            <hr class="my-2" />
            <!-- Profile dropdown inside mobile -->
            <a href="#" class="block py-2 text-gray-700 hover:bg-gray-100">Settings</a>
            <a href="#" class="block py-2 text-gray-700 hover:bg-gray-100">Change Password</a>
            <a href="/" class="block py-2 text-red-600 hover:bg-gray-100">Logout</a>
          </nav>
        </div>
      </transition>
    </header>

    <!-- Page Content -->
    <main class="pt-20 flex-1 bg-gray-50">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const mobileMenuOpen = ref(false);
const profileOpen = ref(false);

// Close profile dropdown when clicking outside
onMounted(() => {
  document.addEventListener("click", (e) => {
    const dropdown = document.querySelector(".relative");
    if (dropdown && !dropdown.contains(e.target)) {
      profileOpen.value = false;
    }
  });
});
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
