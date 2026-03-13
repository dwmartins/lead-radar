<script setup>
import { isAdmin, isUser } from '@/helpers/auth'
import { useAuthStore } from '@/stores/authStore'
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

onMounted(() => {    
    if (isAdmin()) router.replace('/admin/dashboard')
    else if (isUser()) router.replace('/dashboard')
    else router.replace('/entrar')
})
</script>

<template>
    <div></div>
</template>