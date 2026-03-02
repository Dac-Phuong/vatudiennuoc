<script setup>
import ProductBrand from '@/Components/client/Product/ProductBrand.vue';
import ProductFeatured from '@/Components/client/Product/ProductFeatured.vue';
import ProductOutstanding from '@/Components/client/Product/ProductOutstanding.vue';
import HeroSection from '@/Components/client/sections/HeroSection.vue';
import NewsSection from '@/Components/client/sections/NewsSection.vue';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
const { settings } = usePage().props;

const props = defineProps({
    productSale: Object,
    ProductBestSelling: Object,
    productBrands: Object,
    firstNews: Object,
    news: Object
})

</script>
<template>
    <ClientLayout>
        <HeroSection />
        <ProductOutstanding :productSale="productSale" />
        <ProductFeatured :ProductBestSelling="ProductBestSelling" />
        <ProductBrand :productBrands="productBrands" />
        <NewsSection :news="news" :first-news="firstNews" />
    </ClientLayout>
</template>
<style>
.section-title-main {
    text-transform: uppercase;
    font-size: 16px;
    line-height: 16px;
    color: #fff;
    border-radius: 5px 5px 0 0;
    padding: 8px 20px;
    font-weight: bold;
    position: relative;
    margin-right: 20px;
}


.section-title-main:after {
    content: '';
    display: block;
    line-height: 35px;
    background: var(--primary-color);
    width: 18px;
    height: 35px;
    position: absolute;
    top: 0;
    right: 0;
    margin-right: -7px;
    transform: skewX(21deg);
    border-radius: 0 5px 0 0;
}
</style>