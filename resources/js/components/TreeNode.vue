<template>
  <div class="tree-node">
    <!-- Node with popover -->
    <div class="relative">
      <div 
        class="node-content"
        @click="togglePopover"
        :class="{ 'active': node.status === 'ACTIVE', 'inactive': node.status !== 'ACTIVE' }"
      >
        <span class="node-name">{{ node.username || 'Unknown' }}</span>
      </div>
      
      <!-- Popover with node details -->
      <div v-if="showPopover" class="node-popover" @click.stop>
        <div class="popover-header">
          <h3>{{ node.full_name || node.username || 'Unknown User' }}</h3>
        </div>
        <div class="popover-content">
          <p v-if="node.distributor_id"><strong>ID:</strong> {{ node.distributor_id }}</p>
          <p v-if="node.status"><strong>Status:</strong> {{ node.status }}</p>
          <p v-if="node.product_name"><strong>Product:</strong> {{ node.product_name }}</p>
          <p v-if="node.category_name"><strong>Category:</strong> {{ node.category_name }}</p>
          <p v-if="node.parent_username"><strong>Parent:</strong> {{ node.parent_username }}</p>
          <p v-if="node.binary_placement"><strong>Placement:</strong> {{ node.binary_placement }}</p>
          <div class="popover-actions">
            <button @click="makeRoot" class="make-root-btn">View as Root</button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Children nodes -->
    <div v-if="node.children && node.children.length && showChildren" class="node-children">
      <div class="connector-line"></div>
      <div class="children-container">
        <!-- Organizamos los nodos según su binary_placement -->
        <div class="left-child" v-if="getLeftChild">
          <TreeNode 
            :node="getLeftChild"
            :level="level + 1"
            class="child-node"
            @set-as-root="$emit('set-as-root', $event)"
          />
        </div>
        <div class="right-child" v-if="getRightChild">
          <TreeNode 
            :node="getRightChild"
            :level="level + 1"
            class="child-node"
            @set-as-root="$emit('set-as-root', $event)"
          />
        </div>
      </div>
    </div>
    <!-- Message when max level is reached -->
    <div v-else-if="node.children && node.children.length && !showChildren" class="max-level-reached">
      <span>+{{ node.children.length }} more...</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  node: {
    type: Object,
    required: true
  },
  level: {
    type: Number,
    default: 1
  }
});

const emit = defineEmits(['set-as-root']);

const showPopover = ref(false);
const showChildren = computed(() => props.level < 5);

// Propiedades computadas para obtener los hijos izquierdo y derecho
const getLeftChild = computed(() => {
  if (!props.node.children || !props.node.children.length) return null;
  
  // Buscar hijo con placement Left
  const left = props.node.children.find(child => child.binary_placement === 'Left');
  if (left) return left;
  
  // Si hay un hijo con placement Right y es el primero, usar el segundo hijo (si existe)
  const right = props.node.children.find(child => child.binary_placement === 'Right');
  if (right && props.node.children[0] === right && props.node.children.length > 1) {
    return props.node.children[1];
  }
  
  // Por defecto usar el primer hijo
  return null;
});

const getRightChild = computed(() => {
  if (!props.node.children || !props.node.children.length) return null;
  
  // Buscar hijo con placement Right
  const right = props.node.children.find(child => child.binary_placement === 'Right');
  if (right) return right;
  
  // Si solo hay un hijo, no hay hijo derecho
  if (props.node.children.length === 1) return null;
  
  // Por defecto usar el segundo hijo
  return props.node.children[1];
});

const togglePopover = () => {
  showPopover.value = !showPopover.value;
};

const makeRoot = () => {
  emit('set-as-root', props.node);
  showPopover.value = false;
};
</script>

<style scoped>
.tree-node {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin: 0 0.5rem;
}

@media (min-width: 640px) {
  .tree-node {
    margin: 0 1rem;
  }
}

.node-content {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 80px;
  height: 40px;
  border-radius: 6px;
  background-color: #f3f4f6;
  border: 2px solid #d1d5db;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
  z-index: 2;
}

@media (min-width: 640px) {
  .node-content {
    width: 120px;
    height: 60px;
    border-radius: 8px;
  }
}

.node-content:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.node-content.active {
  background-color: #dcfce7;
  border-color: #86efac;
}

.node-content.inactive {
  background-color: #fee2e2;
  border-color: #fca5a5;
}

.node-name {
  font-weight: 600;
  text-align: center;
  overflow: hidden;
  text-overflow: ellipsis;
  padding: 0 2px;
  font-size: 0.65rem;
  line-height: 1.1;
  max-width: 100%;
  word-break: break-word;
}

@media (min-width: 640px) {
  .node-name {
    padding: 0 4px;
    font-size: 0.75rem;
  }
}

.node-popover {
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  width: 200px;
  max-width: 90vw;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  z-index: 10;
  margin-top: 10px;
  overflow: hidden;
}

@media (min-width: 640px) {
  .node-popover {
    width: 220px;
  }
}

.popover-header {
  background-color: #f3f4f6;
  padding: 10px;
  border-bottom: 1px solid #e5e7eb;
}

.popover-header h3 {
  margin: 0;
  font-size: 0.875rem;
  font-weight: 600;
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

@media (min-width: 640px) {
  .popover-header h3 {
    font-size: 1rem;
  }
}

.popover-content {
  padding: 10px;
}

.popover-content p {
  margin: 4px 0;
  font-size: 0.75rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

@media (min-width: 640px) {
  .popover-content p {
    margin: 5px 0;
    font-size: 0.875rem;
  }
}

.popover-actions {
  margin-top: 10px;
  padding-top: 8px;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: center;
}

.make-root-btn {
  background-color: #3b82f6;
  color: white;
  font-size: 0.75rem;
  padding: 4px 8px;
  border-radius: 4px;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s;
}

.make-root-btn:hover {
  background-color: #2563eb;
}

.node-children {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 15px;
  position: relative;
  width: 100%;
}

@media (min-width: 640px) {
  .node-children {
    margin-top: 20px;
  }
}

.connector-line {
  width: 2px;
  height: 15px;
  background-color: #d1d5db;
}

@media (min-width: 640px) {
  .connector-line {
    height: 20px;
  }
}

.children-container {
  display: flex;
  justify-content: center;
  position: relative;
  padding-top: 15px;
  gap: 1rem;
}

@media (min-width: 640px) {
  .children-container {
    padding-top: 20px;
    gap: 2rem;
  }
}

.left-child, .right-child {
  position: relative;
}

.left-child {
  order: 1;
}

.right-child {
  order: 2;
}

.children-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 50%;
  width: 80%;
  height: 2px;
  background-color: #d1d5db;
  transform: translateX(-50%);
}

.child-node {
  position: relative;
}

.child-node::before {
  content: '';
  position: absolute;
  top: -15px;
  left: 50%;
  width: 2px;
  height: 15px;
  background-color: #d1d5db;
  transform: translateX(-50%);
}

@media (min-width: 640px) {
  .child-node::before {
    top: -20px;
    height: 20px;
  }
}

.max-level-reached {
  margin-top: 10px;
  padding: 5px 10px;
  background-color: #f3f4f6;
  border-radius: 4px;
  font-size: 0.875rem;
  color: #6b7280;
  border: 1px dashed #d1d5db;
}
</style>
