<template>
  <div class="text-gray-800 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="fixed top-0 w-full z-50 bg-white backdrop-blur-md text-gray-700 shadow-sm">
		<div class="max-w-7xl mx-auto px-8 py-4 flex justify-between items-center">
			<!-- Logo -->
			<div class="text-2xl font-extrabold">PRMS</div>

			<!-- Desktop Menu (with Profile inside) -->
			<nav class="flex items-center hidden md:flex text-sm">
				<a href="/create_pr" class="text-gray-500 hover:text-gray-600 px-3 py-1.5 rounded-md font-medium">Dashboard</a>
				<a href="/pr_list" class="text-gray-500 hover:text-gray-600 px-3 py-1.5 rounded-md font-medium">PR List</a>

				<!-- Masterfile dropdown -->
				<div class="relative" ref="masterfileRef">
				<button
					@click.stop="toggleMasterfile"
					class="flex items-center space-x-1 text-gray-500 hover:text-gray-600 px-3 py-1.5 rounded-md font-medium focus:outline-none"
				>
					<span>Masterfile</span>
					<svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
					</svg>
				</button>

				<transition name="fade">
					<div
					v-if="masterfileOpen"
					@click.stop
					class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-lg overflow-hidden z-50"
					>
					<a href="/masterfile/items" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Items</a>
					<a href="/masterfile/category" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Category</a>
					<a href="/masterfile/department" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Department</a>
					<a href="/masterfile/enduse" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Enduse</a>
					<a href="/masterfile/purpose" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Purpose</a>
					<a href="/masterfile/employee" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Employee</a>
					<a href="/masterfile/users" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Users</a>
					<a href="/masterfile/location_pr" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Location PR</a>
					<a href="/masterfile/location_item" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Location Item</a>
					<a href="/masterfile/company" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Company</a>
					<a href="/masterfile/warehouse" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Warehouse</a>
					<a href="/masterfile/rack" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Rack</a>
					<a href="/masterfile/qualifier" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Qualifier</a>
					<a href="/masterfile/group" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Group</a>
					</div>
				</transition>
				</div>

				<!-- Profile dropdown -->
				<div class="relative ml-6" ref="profileRef">
				<button @click.stop="toggleProfile" class="flex items-center space-x-2 focus:outline-none">
					<img src="https://i.pravatar.cc/40" alt="Profile" class="w-8 h-8 rounded-full" />
					<span class="font-bold">John Doe</span>
					<svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
					</svg>
				</button>

				<transition name="fade">
					<div
					v-if="profileOpen"
					@click.stop
					class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg overflow-hidden z-50"
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
import { ref, onMounted, onUnmounted  } from "vue";

const mobileMenuOpen = ref(false);
const masterfileOpen = ref(false);
const profileOpen = ref(false);

// element refs for click-outside detection
const masterfileRef = ref(null);
const profileRef = ref(null);

function toggleMasterfile() {
  masterfileOpen.value = !masterfileOpen.value;
  // close other dropdown(s) if needed
  if (masterfileOpen.value) profileOpen.value = false;
}
function toggleProfile() {
  profileOpen.value = !profileOpen.value;
  if (profileOpen.value) masterfileOpen.value = false;
}

// small composable: call callback when clicking outside elementRef
function useClickOutside(elementRef, callback) {
  const handler = (e) => {
    const el = elementRef.value;
    if (!el) return;
    if (!el.contains(e.target)) callback();
  };
  onMounted(() => document.addEventListener('click', handler));
  onUnmounted(() => document.removeEventListener('click', handler));
}

useClickOutside(masterfileRef, () => (masterfileOpen.value = false));
useClickOutside(profileRef, () => (profileOpen.value = false));

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
