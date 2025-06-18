<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import TreeNode from '@/components/TreeNode.vue';

const page = usePage();
const originalTreeData = ref(null);
const currentTreeData = ref(null);
const isViewingSubtree = ref(false);

onMounted(() => {
  // Get the tree data from the page props
  if (page.props.tree && page.props.tree.root) {
    originalTreeData.value = page.props.tree.root;
    currentTreeData.value = originalTreeData.value;
  }
});

// Function to set a node as the new root
const setAsRoot = (node) => {
  // Forzar una actualización completa del componente
  currentTreeData.value = null;
  
  // Usar setTimeout para asegurar que el DOM se actualice
  setTimeout(() => {
    // Crear una copia profunda del nodo para evitar problemas de referencia
    currentTreeData.value = JSON.parse(JSON.stringify(node));
    isViewingSubtree.value = true;
  }, 0);
};

// Function to reset to the original tree
const resetToOriginalTree = () => {
  currentTreeData.value = originalTreeData.value;
  isViewingSubtree.value = false;
};
</script>

<template>
  <div class="viewer-page">
    <div class="tree-container">
      <div class="header-section">
        <h1 class="text-xl md:text-2xl font-bold">Genealogy Tree Viewer</h1>
        <button 
          v-if="isViewingSubtree" 
          @click="resetToOriginalTree"
          class="reset-button"
        >
          View Complete Tree
        </button>
      </div>
      
      <div v-if="currentTreeData" class="tree-wrapper">
        <TreeNode 
          :node="currentTreeData" 
          @set-as-root="setAsRoot"
        />
      </div>
      <div v-else class="flex justify-center items-center h-64">
        <p class="text-gray-500">No tree data available</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.viewer-page {
  min-height: 100vh;
  width: 100%;
  display: flex;
  flex-direction: column;
  background-color: #f9fafb;
  overflow: hidden;
}

.tree-container {
  flex: 1;
  padding: 1rem;
  width: 100%;
  max-width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

@media (min-width: 640px) {
  .tree-container {
    padding: 2rem;
  }
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  flex-wrap: wrap;
  gap: 0.5rem;
}

@media (min-width: 640px) {
  .header-section {
    margin-bottom: 2rem;
    flex-wrap: nowrap;
  }
}

.reset-button {
  background-color: #4f46e5;
  color: white;
  padding: 0.5rem 0.75rem;
  border-radius: 0.375rem;
  font-weight: 500;
  transition: background-color 0.2s;
  border: none;
  cursor: pointer;
  font-size: 0.875rem;
  white-space: nowrap;
}

@media (min-width: 640px) {
  .reset-button {
    padding: 0.5rem 1rem;
    font-size: 1rem;
  }
}

.reset-button:hover {
  background-color: #4338ca;
}

.tree-wrapper {
  display: flex;
  justify-content: center;
  padding: 0.5rem 0;
  min-width: fit-content;
  touch-action: pan-x pan-y pinch-zoom;
}
</style>
