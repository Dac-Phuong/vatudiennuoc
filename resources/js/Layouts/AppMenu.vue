<script setup>
import { ref } from 'vue';

import AppMenuItem from './AppMenuItem.vue';
const model = ref([
    {
        label: 'Quản lý',
        items: [
            { label: 'Thống kê', icon: 'pi pi-fw pi-home', to: '/@admin' },
            { label: 'Đơn hàng', icon: 'pi pi-fw pi-shopping-cart', to: '/@admin/orders' },
            { label: 'Cài đặt', icon: 'pi pi-fw pi-cog', to: '/@admin/settings' }
        ]
    },
    {
        label: 'Quản lý người dùng',
        items: [
            { label: 'Người dùng', icon: 'pi pi-fw pi-user', to: '/@admin/users', },
        ]
    },
    {
        label: 'Quản lý danh mục',
        icon: 'pi pi-fw pi-briefcase',
        to: '/@admin/categories',
        items: [
            {
                label: 'Bài viết',
                icon: 'pi pi-fw pi-book',
                items: [
                    {
                        label: 'Danh mục',
                        icon: 'pi pi-fw pi-list',
                        to: '/@admin/news-categories',

                    },
                    {
                        label: 'Danh sách bài viết',
                        icon: 'pi pi-fw pi-list',
                        to: '/@admin/news',

                    },
                ]
            },
            {
                label: 'Sản phẩm',
                icon: 'pi pi-fw pi-box',
                items: [
                    {
                        label: 'Danh mục',
                        icon: 'pi pi-fw pi-list',
                        to: '/@admin/product-categories',

                    },
                    {
                        label: 'Thương hiệu',
                        icon: 'pi pi-fw pi-list',
                        to: '/@admin/product-brands',

                    },
                    {
                        label: 'Danh sách sản phẩm',
                        icon: 'pi pi-fw pi-list',
                        to: '/@admin/products',

                    },
                ]
            },

        ]
    },
    {
        label: 'Nhật ký',
        items: [
            {
                label: 'Nhật ký admin',
                icon: 'pi pi-fw pi-history',
                to: '/@admin/admin-logs',
            },
            // {
            //     label: 'Nhật ký user',
            //     icon: 'pi pi-fw pi-history',
            //     to: '/@admin/user-logs',
            // },
        ]
    },


]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <AppMenuItem v-if="!item.separator" :item="item" :index="i"></AppMenuItem>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
