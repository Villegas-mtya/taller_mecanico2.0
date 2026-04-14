<script setup>
import { computed } from "vue";

const props = defineProps({
  type: {
    type: String,
    default: "button",
  },
  variant: {
    type: String,
    default: "primary",
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  block: {
    type: Boolean,
    default: false,
  },
});

const classes = computed(() => [
  "base-btn",
  `base-btn--${props.variant}`,
  {
    "base-btn--block": props.block,
    "base-btn--disabled": props.disabled,
  },
]);
</script>

<template>
  <button :type="type" :disabled="disabled" :class="classes">
    <slot />
  </button>
</template>

<style scoped>
.base-btn {
  min-height: 44px;
  padding: 10px 16px;
  border-radius: 12px;
  border: 1px solid transparent;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.base-btn--block {
  width: 100%;
}

.base-btn--disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.base-btn--primary {
  background: #4f46e5;
  color: #fff;
}

.base-btn--primary:hover {
  background: #4338ca;
}

.base-btn--secondary {
  background: #e2e8f0;
  color: #0f172a;
  border-color: #cbd5e1;
}

.base-btn--secondary:hover {
  background: #cbd5e1;
}

.base-btn--success {
  background: #0f766e;
  color: #fff;
}

.base-btn--success:hover {
  background: #115e59;
}

.base-btn--danger {
  background: #fee2e2;
  color: #b91c1c;
}

.base-btn--danger:hover {
  background: #fecaca;
}

.base-btn--ghost {
  background: transparent;
  color: #334155;
  border-color: #cbd5e1;
}

.base-btn--ghost:hover {
  background: #f8fafc;
}
</style>