<script setup>
import { ref, computed } from 'vue'
import useNotify from '@/Composables/useNotify';
import { router, useForm } from '@inertiajs/vue3';

import { useConfirm } from 'primevue';

const props = defineProps({
    locale: String,
    translations: Object,
    settings: Object,
})
const notify = useNotify();
const loading = ref(false);
const confirm = useConfirm()

const form = useForm({
    // ===== BASIC =====
    is_landing_page: props.settings?.is_landing_page == 1 ? true : false,
    name_brand: props.settings?.name_brand ?? '',
    title_web: props.settings?.title_web ?? '',
    des_web: props.settings?.des_web ?? '',
    favicon: props.settings?.favicon ?? '',
    logo: props.settings?.logo ?? '',
    homepage_img: props.settings?.homepage_img ?? '',
    image_seo: props.settings?.image_seo ?? '',
    mota_home: props.settings?.mota_home ?? '',
    layout_homepage: props.settings?.layout_homepage ?? 'default',

    // ===== META SEO =====
    meta_keywords: props.settings?.meta_keywords ?? '',
    og_url: props.settings?.og_url ?? '',
    site_name: props.settings?.site_name ?? '',
    og_type: props.settings?.og_type ?? 'website',
    og_title: props.settings?.og_title ?? '',
    og_description: props.settings?.og_description ?? '',
    og_image: props.settings?.og_image ?? '',
    // ===== GOOGLE / TRACKING =====
    google_site_verification: props.settings?.google_site_verification ?? '',
    google_analytics_id: props.settings?.google_analytics_id ?? '',
    google_ads_id: props.settings?.google_ads_id ?? '',
    google_tag_manager_id: props.settings?.google_tag_manager_id ?? '',
    google_map_link: props.settings?.google_map_link ?? '',
});


const previewUrls = new Map();
const selectLayout = (value) => {
    form.layout_homepage = value
}
const getImageLink = () => {
    window.open('https://linkanh.xyz/', '_blank');
};
const previewImage = (file) => {
    if (!file) return null;

    if (typeof file === 'string') {
        return file;
    }
    if (!(file instanceof File || file instanceof Blob)) {
        return null;
    }
    if (previewUrls.has(file)) {
        return previewUrls.get(file);
    }
    const url = URL.createObjectURL(file);
    previewUrls.set(file, url);
    return url;
};


const onSelectImage = (event, type = 1) => {
    const file = event.files?.[0];
    if (!file) return;
    if (type === 1) {
        form.homepage_img = file;
    } else if (type === 2) {
        form.image_seo = file;
    } else if (type === 3) {
        form.favicon = file;
    } else if (type === 4) {
        form.logo = file;
    }
};

const submitForm = () => {
    loading.value = true
    const payload = {
        ...form,
    }
    form.transform(() => payload).post(route('admin.settings.update'), {
        onError: (errors) => {
            Object.values(errors).flat().forEach(msg => {
                notify.error(msg)
            })
        },
        onSuccess: () => {
            notify.success('Đã lưu cấu hình thành công')
        },
        onFinish: () => {
            loading.value = false
        },
    })
}

const deleteImage = (key) => {
    confirm.require({
        message: 'Bạn có chắc chắn muốn xóa hình ảnh này không?',
        header: 'Xác nhận Xóa',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Có',
        rejectLabel: 'Không',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.post(route('image.delete', { key }), {}, {
                onSuccess: () => {
                    form[key] = null;
                    notify.success('Delete image successful');
                },
                onError: (errors) => {
                    Object.values(errors).flat().forEach(err => {
                        notify.error(err);
                    });
                }
            });
        }
    });
};
</script>
<template>

    <form class="space-y-4" @submit.prevent="submitForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- BRAND -->
            <div>
                <label class="label-sm pb-1 flex">{{ 'Tên thương hiệu' }}</label>
                <InputText v-model="form.name_brand" class="w-full" />
            </div>

            <!-- TITLE -->
            <div>
                <label class="label-sm pb-1 flex">{{ 'Tiêu đề Website' }}</label>
                <InputText v-model="form.title_web" class="w-full" />
            </div>

            <!-- OG TITLE -->
            <div>
                <label class="label-sm pb-1 flex">Meta og:title</label>
                <InputText v-model="form.og_title" class="w-full" />
            </div>
            <div>
                <label class="label-sm pb-1 flex">Meta og:site_name</label>
                <InputText v-model="form.site_name" class="w-full" />
            </div>
            <!-- OG TYPE -->
            <div>
                <label class="label-sm pb-1 flex">Meta og:type</label>
                <InputText v-model="form.og_type" class="w-full" />
            </div>
            <div>
                <label class="label-sm pb-1 flex">Meta og:url</label>
                <InputText v-model="form.og_url" placeholder="https://yourdomain.com" class="w-full" />
            </div>
            <!-- OG IMAGE -->
            <div class="md:col-span-2">
                <label class="label-sm pb-1 flex">Meta og:image</label>
                <InputGroup>
                    <InputText placeholder="https://yourdomain.com/seo-image.jpg" v-model="form.og_image" />
                    <Button icon="pi pi-image" label="Get link" @click="getImageLink()"
                        style="width: 100px !important;" />
                </InputGroup>
            </div>
            <!-- OG DESCRIPTION -->
            <div class="md:col-span-2">
                <label class="label-sm pb-1 flex">Meta og:description</label>
                <Textarea v-model="form.og_description" class="w-full" placeholder="" />
            </div>
            <!-- KEYWORDS -->
            <div class="md:col-span-2">
                <label class="label-sm pb-1 flex">Meta Keywords</label>
                <Textarea v-model="form.meta_keywords" class="w-full" rows="2"
                    placeholder="keyword1, keyword2, keyword3" />
            </div>
            <!-- GOOGLE SITE VERIFICATION -->
            <div>
                <label class="label-sm pb-1 flex">
                    Google Site Verification
                </label>
                <InputText v-model="form.google_site_verification" class="w-full" placeholder="vd: Xk12AbCdeF..." />
            </div>

            <!-- GOOGLE ANALYTICS -->
            <div>
                <label class="label-sm pb-1 flex">
                    Google Analytics ID
                </label>
                <InputText v-model="form.google_analytics_id" class="w-full" placeholder="G-XXXXXXXXXX" />
            </div>

            <!-- GOOGLE ADS -->
            <div>
                <label class="label-sm pb-1 flex">
                    Google Ads Tag ID
                </label>
                <InputText v-model="form.google_ads_id" class="w-full" placeholder="AW-XXXXXXXXX" />
            </div>

            <!-- TAG MANAGER -->
            <div>
                <label class="label-sm pb-1 flex">
                    Google Tag Manager ID
                </label>
                <InputText v-model="form.google_tag_manager_id" class="w-full" placeholder="GTM-XXXXXXX" />
            </div>

        </div>
        <div>
                <label class="label-sm pb-1 flex">
                    Link google map
                </label>
                <InputText v-model="form.google_map_link" class="w-full" placeholder="https://maps.google.com/..." />
            </div>

        <!-- 
        
          -->
        <div class="flex flex-wrap gap-4">
            <!-- Favicon -->
            <div>
                <label class="label-sm pb-1 flex">{{ 'Favicon' }}</label>
                <FileUpload mode="basic" accept="image/*" customUpload auto severity="secondary"
                    chooseLabel="Choose Image" @select="(e) => onSelectImage(e, 3)" />
                <div v-if="form.favicon" class="relative group w-fit mt-2">
                    <img :src="previewImage(form.favicon)" class="h-24 rounded-lg object-cover border shadow-xl" />
                    <div @click="deleteImage('favicon')" class="absolute top-1 right-1
                        bg-red-600 hover:bg-red-700
                        text-white w-6 h-6 p-5 m-auto cursor-pointer
                        flex items-center justify-center
                        rounded-full
                        opacity-0 group-hover:opacity-100
                        transition">
                        <i class="pi pi-trash" />
                    </div>
                </div>
            </div>

            <!-- Logo -->
            <div>
                <label class="label-sm pb-1 flex">{{ 'Logo' }}</label>
                <FileUpload mode="basic" accept="image/*" customUpload auto severity="secondary"
                    chooseLabel="Choose Image" @select="(e) => onSelectImage(e, 4)" />
                <div v-if="form.logo" class="relative group w-fit mt-2">
                    <img :src="previewImage(form.logo)" class="h-24 rounded-lg object-cover border shadow-xl" />
                    <div @click="deleteImage('logo')" class="absolute top-1 right-1
                        bg-red-600 hover:bg-red-700
                        text-white w-6 h-6 p-5 m-auto cursor-pointer
                        flex items-center justify-center
                        rounded-full
                        opacity-0 group-hover:opacity-100
                        transition">
                        <i class="pi pi-trash" />
                    </div>
                </div>
            </div>
            <!-- Image SEO -->
            <div>
                <label class="label-sm pb-1 flex">{{ 'Image SEO' }}</label>
                <FileUpload mode="basic" accept="image/*" customUpload auto severity="secondary"
                    chooseLabel="Choose Image" @select="(e) => onSelectImage(e, 2)" />
                <div v-if="form.image_seo" class="relative group w-fit mt-2">
                    <img :src="previewImage(form.image_seo)" class="h-24 rounded-lg object-cover border shadow-xl" />
                    <div @click="deleteImage('image_seo')" class="absolute top-1 right-1
                        bg-red-600 hover:bg-red-700
                        text-white w-6 h-6 p-5 m-auto cursor-pointer
                        flex items-center justify-center
                        rounded-full
                        opacity-0 group-hover:opacity-100
                        transition">
                        <i class="pi pi-trash" />
                    </div>
                </div>
            </div>
            <div>
                <label class="label-sm pb-1 flex">{{ 'Image Home' }}</label>
                <FileUpload mode="basic" accept="image/*" customUpload auto severity="secondary"
                    chooseLabel="Choose Image" @select="(e) => onSelectImage(e, 1)" />
                <div v-if="form.homepage_img" class="relative group w-fit mt-2">
                    <img :src="previewImage(form.homepage_img)" class="h-24 rounded-lg object-cover border shadow-xl" />
                    <div @click="deleteImage('homepage_img')" class="absolute top-1 right-1
                        bg-red-600 hover:bg-red-700
                        text-white w-6 h-6 p-5 m-auto cursor-pointer
                        flex items-center justify-center
                        rounded-full
                        opacity-0 group-hover:opacity-100
                        transition">
                        <i class="pi pi-trash" />
                    </div>
                </div>

            </div>
        </div>

        <div class="text-right">
            <Button label="Save Config" :loading="loading" type="submit" icon="pi pi-save" />
        </div>
    </form>

</template>
<style>
.layout-img .p-image {
    width: 100%;
    height: 100%;
    display: block;
}

.layout-img img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
    object-position: top;
}
</style>