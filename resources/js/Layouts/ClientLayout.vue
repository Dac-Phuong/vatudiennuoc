<script setup>
import ClientFooter from '@/Components/client/Footer/ClientFooter.vue';
import ClientHeader from '@/Components/client/Header/ClientHeader.vue';
import FloatingConfigurator from '@/Components/FloatingConfigurator.vue';

</script>
<template>
    <FloatingConfigurator />
    <div class="client-layout">
        <ClientHeader />
        <main class="client-main pt-[90px] md:pt-[110px]">
            <slot></slot>
        </main>
        <ClientFooter />
    </div>
    <FloatingConfigurator />
    <Toast />
</template>

<style scoped>
.client-layout {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}
</style>