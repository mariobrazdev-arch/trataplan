<script setup>
import { ref } from 'vue';

const props = defineProps({
    existing: { type: Array, default: () => [] },
    deleteUrlPrefix: { type: String, required: true },
    modelValue: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:modelValue']);

const previews = ref([]); // { file, url }

function addFiles(e) {
    for (const file of e.target.files) {
        previews.value.push({ file, url: URL.createObjectURL(file) });
    }
    e.target.value = null;
    emit('update:modelValue', previews.value.map(p => p.file));
}

function removePending(index) {
    URL.revokeObjectURL(previews.value[index].url);
    previews.value.splice(index, 1);
    emit('update:modelValue', previews.value.map(p => p.file));
}
</script>

<template>
    <div class="space-y-3">
        <!-- Existing photos -->
        <div v-if="existing?.length" class="flex flex-wrap gap-3">
            <div v-for="photo in existing" :key="photo.id"
                class="relative rounded-xl overflow-hidden" style="width: 120px; height: 90px;">
                <img :src="photo.url" class="w-full h-full object-cover" />
                <a :href="`${deleteUrlPrefix}/${photo.id}`"
                    onclick="return confirm('Remover esta foto?')"
                    class="absolute top-1 right-1 w-6 h-6 flex items-center justify-center rounded-full text-white text-xs"
                    style="background-color: rgba(186,26,26,0.85);"
                    data-method="delete">
                    <span class="material-symbols-outlined text-sm">close</span>
                </a>
            </div>
        </div>

        <!-- Pending new photos -->
        <div v-if="previews.length" class="flex flex-wrap gap-3">
            <div v-for="(p, i) in previews" :key="i"
                class="relative rounded-xl overflow-hidden" style="width: 120px; height: 90px;">
                <img :src="p.url" class="w-full h-full object-cover" />
                <button type="button" @click="removePending(i)"
                    class="absolute top-1 right-1 w-6 h-6 flex items-center justify-center rounded-full text-white text-xs"
                    style="background-color: rgba(186,26,26,0.85);">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
                <span class="absolute bottom-0 left-0 right-0 text-center text-xs py-0.5 text-white"
                    style="background-color: rgba(0,0,0,0.45);">Nova</span>
            </div>
        </div>

        <!-- Add more -->
        <label class="inline-flex items-center gap-2 cursor-pointer px-4 py-2.5 rounded-lg text-sm font-bold"
            style="background-color: #f0ede8; color: #012d1d;">
            <span class="material-symbols-outlined text-base">add_photo_alternate</span>
            Adicionar fotos
            <input type="file" multiple accept="image/jpeg,image/png,image/webp" class="hidden" @change="addFiles" />
        </label>
        <p class="text-xs" style="color: #717973;">JPG, PNG ou WebP. Máx. 3 MB por foto.</p>
    </div>
</template>
