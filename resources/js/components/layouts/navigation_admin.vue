<template>
  <div class="flex min-h-screen text-gray-800">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg fixed top-0 left-0 h-full z-40 flex flex-col">
      <!-- Logo -->
      <div class="p-4 text-xl font-bold border-b">PRMS</div>

      <!-- Nav Links -->
      <nav class="flex-1 flex flex-col space-y-2 p-2">
        <!-- Single menu link -->
        <a href="#" class="px-6 py-2 rounded-lg hover:bg-gray-100">Dashboard</a>

        <!-- Dropdown menu -->
        <div>
        <button
            @click="requestsOpen = !requestsOpen"
            class="w-full flex items-center justify-between px-6 py-2 rounded-lg hover:bg-gray-100 focus:outline-none"
        >
            <span>Masterfile</span>
            <svg
            :class="{ 'rotate-180': requestsOpen }"
            class="w-4 h-4 transform transition-transform"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
            >
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Submenu -->
        <transition name="fade">
            <div v-if="requestsOpen" class="ml-6 mt-1 flex flex-col space-y-1">
            <a href="#" class="px-4 py-2 text-sm rounded-lg hover:bg-gray-100">Items</a>
            <a href="#" class="px-4 py-2 text-sm rounded-lg hover:bg-gray-100">Department</a>
            <a href="#" class="px-4 py-2 text-sm rounded-lg hover:bg-gray-100">Purpose</a>
            <a href="#" class="px-4 py-2 text-sm rounded-lg hover:bg-gray-100">Enduse</a>
            <a href="#" class="px-4 py-2 text-sm rounded-lg hover:bg-gray-100">Employees</a>
            <a href="#" class="px-4 py-2 text-sm rounded-lg hover:bg-gray-100">Users</a>
            <a href="#" class="px-4 py-2 text-sm rounded-lg hover:bg-gray-100">Location</a>
            <a href="#" class="px-4 py-2 text-sm rounded-lg hover:bg-gray-100">Company</a>
            </div>
        </transition>
        </div>

        <!-- Another section -->
        <a href="#" class="px-6 py-2 rounded-lg hover:bg-gray-100">Reports</a>
        <a href="#" class="px-6 py-2 rounded-lg hover:bg-gray-100">Settings</a>
    </nav>

      <!-- Profile Dropdown -->
      <div class="p-4 border-t relative">
        <button @click="profileOpen = !profileOpen" class="flex items-center space-x-2 w-full focus:outline-none">
          <img src="https://i.pravatar.cc/40" alt="Profile" class="w-8 h-8 rounded-full">
          <span class="font-medium flex-1 text-left">John Doe</span>
          <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <!-- Dropdown -->
        <transition name="fade">
          <div
            v-if="profileOpen"
            class="absolute bottom-16 left-4 w-56 bg-white rounded-xl shadow-lg overflow-hidden"
          >
            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Settings</a>
            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Change Password</a>
            <a href="/" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Logout</a>
          </div>
        </transition>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 p-6 bg-gray-50">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const profileOpen = ref(false);
const requestsOpen = ref(false);

// Close profile dropdown when clicking outside
onMounted(() => {
  document.addEventListener("click", (e) => {
    const dropdown = document.querySelector("aside .relative");
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
