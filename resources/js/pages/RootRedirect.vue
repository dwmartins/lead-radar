<script setup>
import { isAdmin, isUser } from '@/helpers/auth'
import { useAuthStore } from '@/stores/authStore'
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

onMounted(() => {    
    if (isAdmin()) router.replace({ name: 'admin.dashboard' })
    else if (isUser()) router.replace({ name: 'dashboard' })
    else router.replace({ name: 'login' })
});

</script>

<template>
    <div></div>
</template>