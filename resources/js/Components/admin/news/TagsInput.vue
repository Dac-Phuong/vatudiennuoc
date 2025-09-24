<script setup>
import { ref, watch, defineProps, defineEmits } from "vue";
import Chip from "primevue/chip";

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: "Nhập tag, nhấn , hoặc Enter",
    },
});

const emit = defineEmits(["update:modelValue"]);

const tags = ref([...props.modelValue]);
const newTag = ref("");

// đồng bộ props ↔ local state
watch(
    () => props.modelValue,
    (val) => {
        tags.value = [...val];
    }
);

const addTag = () => {
    const tag = newTag.value.replace(",", "").trim();
    if (tag && !tags.value.includes(tag)) {
        tags.value.push(tag);
        emit("update:modelValue", tags.value);
    }
    newTag.value = "";
};

const handleKey = (e) => {
    if (e.key === "," || e.key === "Enter") {
        addTag();
        e.preventDefault();
    }
};

const removeTag = (tag) => {
    tags.value = tags.value.filter((t) => t !== tag);
    emit("update:modelValue", tags.value);
};
</script>

<template>
    <div class="flex flex-wrap items-center gap-1">
        <InputText v-model="newTag" @keydown="handleKey" placeholder="Nhập tag..." class="outline-none w-full mb-1" />
        <Chip v-for="tag in tags" :key="tag" :label="tag" removable @remove="removeTag(tag)" />
    </div>
</template>
