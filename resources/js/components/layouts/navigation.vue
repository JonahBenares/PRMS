<script setup>
import { data } from "jquery";
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter } from 'vue-router';
const router = useRouter();
let credentials = ref([]);

const mobileMenuOpen = ref(false);
const masterfileOpen = ref(false);
const profileOpen = ref(false);
const masterfileRef = ref(null);
const profileRef = ref(null);


onMounted(async () => {
	getDashboard();
});

const getDashboard = async () => {
    const token = localStorage.getItem('token'); // Get token
    if (!token) {
        router.push('/');
        return;
    }

    const response = await fetch(`/api/dashboard`, {
        headers: {
            'Authorization': `Bearer ${token}`, // Pass the token
            'Accept': 'application/json'
        }
    });

    credentials.value = await response.json();

    if (!credentials.value.name) {
        alert('You have been logged out due to inactivity.')
        localStorage.removeItem('token');
        router.push('/');
    }
};


const masterfileLinks = [
  { text: "Items", href: "/masterfile/items" },
  { text: "Category", href: "/masterfile/category" },
  { text: "Department", href: "/masterfile/department" },
  { text: "Enduse", href: "/masterfile/enduse" },
  { text: "Purpose", href: "/masterfile/purpose" },
  { text: "Employee", href: "/masterfile/employee" },
  { text: "Users", href: "/masterfile/users" },
  { text: "Location PR", href: "/masterfile/location_pr" },
  { text: "Company", href: "/masterfile/company" },
  { text: "Qualifier", href: "/masterfile/qualifier" },
  { text: "Signatories", href: "/masterfile/signatories" },
];

function toggleMasterfile() {
  masterfileOpen.value = !masterfileOpen.value;
  if (masterfileOpen.value) profileOpen.value = false;
}
function toggleProfile() {
  profileOpen.value = !profileOpen.value;
  if (profileOpen.value) masterfileOpen.value = false;
}

// Detect click outside
function useClickOutside(elRef, callback) {
  const handler = (e) => {
    if (elRef.value && !elRef.value.contains(e.target)) callback();
  };
  onMounted(() => document.addEventListener("click", handler));
  onUnmounted(() => document.removeEventListener("click", handler));
}

useClickOutside(masterfileRef, () => (masterfileOpen.value = false));
useClickOutside(profileRef, () => (profileOpen.value = false));


const logout = async () => {
	loading.value = true;
	setTimeout(() => {
		localStorage.removeItem('token');
		router.push('/');
		loading.value = false;
	}, 1500);
};
</script>

<template>
	<div class="text-gray-800 min-h-screen flex flex-col ">
		<!-- Header -->
		<header class="fixed top-0 w-full z-50 bg-gradient-to-bl from-blue-950 to-blue-900 backdrop-blur-xl border-b border-blue-200/60 shadow-sm " >
			<div class="max-w-7xl mx-auto px-6 lg:px-10 py-3 flex justify-between items-center" >
				<!-- Logo -->
				<div class="text-2xl font-extrabold bg-white bg-clip-text text-transparent">
					PRMS
				</div>

				<!-- Desktop Menu -->
				<nav class="hidden md:flex items-center space-x-1 text-sm font-medium">
					<a href="/create_pr" class="px-3 py-2 rounded-lg text-white hover:text-gray-900 hover:bg-gray-100 transition-all duration-200" >
						Dashboard
					</a>
					<a href="/pr_list" class="px-3 py-2 rounded-lg text-white hover:text-gray-900 hover:bg-gray-100 transition-all duration-200" >
						PR List
					</a>

					<!-- Masterfile Dropdown -->
					<div class="relative" ref="masterfileRef">
						<button @click.stop="toggleMasterfile" class="flex items-center gap-1 px-3 py-2 rounded-lg text-white hover:text-gray-900 hover:bg-gray-100 transition-all duration-200" >
						<span>Masterfile</span>
						<svg
							class="w-4 h-4 transition-transform duration-200"
							:class="{ 'rotate-180': masterfileOpen }"
							fill="none"
							stroke="currentColor"
							stroke-width="2"
							viewBox="0 0 24 24"
						>
							<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
						</svg>
						</button>

						<transition name="fade-slide">
						<div
							v-if="masterfileOpen"
							@click.stop
							class="absolute left-0 mt-2 w-52 bg-white py-2 backdrop-blur-lg rounded-xl shadow-xl border border-gray-100 overflow-hidden animate-fadeIn"
						>
							<template v-for="item in masterfileLinks" :key="item.text">
							<a
								:href="item.href"
								class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-all"
							>
								{{ item.text }}
							</a>
							</template>
						</div>
						</transition>
					</div>

					<!-- Profile Dropdown -->
					<div class="relative ml-4" ref="profileRef">
						<button
							@click.stop="toggleProfile"
							class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-all text-white hover:text-gray-900"
							>
							<img
								src="https://i.pravatar.cc/40"
								alt="Profile"
								class="w-8 h-8 rounded-full ring-2 ring-white"
							/>
							<span class="font-semibold">{{ credentials.name }}</span>
							<svg
								class="w-4 h-4  transition-transform duration-200"
								:class="{ 'rotate-180': profileOpen }"
								fill="none"
								stroke="currentColor"
								stroke-width="2"
								viewBox="0 0 24 24"
							>
								<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
							</svg>
						</button>

						<transition name="fade-slide">
						<div
							v-if="profileOpen"
							@click.stop
							class="absolute right-0 mt-2 w-48 py-2 bg-white backdrop-blur-lg border border-gray-100 rounded-xl shadow-xl overflow-hidden animate-fadeIn"
						>
							<a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-all">Settings</a>
							<a href="/change_password/" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-all">Change Password</a>
							<a href="/" class="block px-4 py-2 text-red-600 hover:bg-gray-100 transition-all">Logout</a>
						</div>
						</transition>
					</div>
				</nav>

				<!-- Mobile Menu Toggle -->
				<button
				@click="mobileMenuOpen = !mobileMenuOpen"
				class="md:hidden flex items-center justify-center p-2 rounded-lg hover:bg-gray-100 transition-all"
				>
				<svg
					v-if="!mobileMenuOpen"
					xmlns="http://www.w3.org/2000/svg"
					class="w-6 h-6 text-gray-700"
					fill="none"
					viewBox="0 0 24 24"
					stroke="currentColor"
				>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M4 6h16M4 12h16M4 18h16" />
				</svg>
				<svg
					v-else
					xmlns="http://www.w3.org/2000/svg"
					class="w-6 h-6 text-gray-700"
					fill="none"
					viewBox="0 0 24 24"
					stroke="currentColor"
				>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M6 18L18 6M6 6l12 12" />
				</svg>
				</button>
			</div>

			<!-- Mobile Menu -->
			<transition name="fade-slide">
				<div
				v-if="mobileMenuOpen"
				class="md:hidden bg-white backdrop-blur-lg border-t border-gray-100 shadow-inner px-6 py-3 animate-slideDown"
				>
				<nav class="flex flex-col space-y-2 text-sm">
					<a href="/create_pr" class="py-2 text-gray-700 hover:text-blue-600">Dashboard</a>
					<a href="/pr_list" class="py-2 text-gray-700 hover:text-blue-600">PR List</a>
					<a href="#" class="py-2 text-gray-700 hover:text-blue-600">Settings</a>
					<a href="#" class="py-2 text-gray-700 hover:text-blue-600">Change Password</a>
					<a href="#" class="py-2 text-red-600 hover:text-red-700" @click="logout">Logout</a>
				</nav>
				</div>
			</transition>
		</header>

		<!-- Page Content -->
		<main
			class="pt-20 flex-1 relative overflow-hidden bg-gray-100 animated-bg
					bg-[radial-gradient(circle,rgba(0,128,255,0.15)_1px,transparent_1px)]
					bg-[size:24px_24px]"
			>
			<div class="relative z-10">
				<slot />
			</div>
		</main>
	</div>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.25s ease;
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(-5px);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fadeIn {
  animation: fadeIn 0.25s ease;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-slideDown {
  animation: slideDown 0.3s ease;
}

@keyframes glowPulse {
  0%, 100% { transform: scale(1); opacity: 0.8; }
  50% { transform: scale(1.05); opacity: 1; }
}
main > div > div {
  animation: glowPulse 8s ease-in-out infinite alternate;
}
</style>