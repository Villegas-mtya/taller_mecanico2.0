<script setup>
const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: "",
  },
  label: {
    type: String,
    default: "",
  },
  id: {
    type: String,
    default: "",
  },
  type: {
    type: String,
    default: "text",
  },
  placeholder: {
    type: String,
    default: "",
  },
  required: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue"]);
</script>

<template>
  <div class="base-field">
    <label v-if="label" :for="id" class="base-label">
      {{ label }}
    </label>

    <input
      :id="id"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      class="base-input"
      @input="emit('update:modelValue', $event.target.value)"
    />
  </div>
</template>

<style scoped>
.base-field {
  display: grid;
  gap: 8px;
}

.base-label {
  color: #334155;
  font-weight: 600;
  font-size: 14px;
}

.base-input {
  width: 100%;
  min-height: 48px;
  padding: 12px 14px;
  background: #fff;
  color: #0f172a;
  border: 1px solid #cbd5e1;
  border-radius: 12px;
  outline: none;
  transition: 0.2s ease;
}

.base-input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
}
</style>