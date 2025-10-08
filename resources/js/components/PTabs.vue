<script setup>
  import { computed, ref, useTemplateRef, isRef, isReactive, watchEffect } from 'vue'
  import focusWithArrows from '/resources/js/composables/focusWithArrows'

  const props = defineProps({
    titles: { type: Array, required: true },
  })

  const tablist = useTemplateRef('tablist')
  const visibleTab = ref(0)

  const focusedElement = focusWithArrows(tablist)

  watchEffect(() => {
    if (focusedElement.value) {
      visibleTab.value = tablist.value.indexOf(focusedElement.value)
    }
  })

  const tabs = computed(() => props.titles.map((title, i) => ({
    title,
    selected: i === visibleTab.value,
    tabindex: i === visibleTab.value ? 0 : -1,

    buttonId: `tab-${i + 1}`,
    panelId: `tabpanel-${i + 1}`,
  })))

  const prev = () => {
    const selected = tabs.value.findIndex(e => e.selected)
    if (selected) {
      focusedElement.value = tablist.value[selected - 1]
    }
  }

  const next = () => {
    const selected = tabs.value.findIndex(e => e.selected)
    if (selected < tabs.value.length) {
      focusedElement.value = tablist.value[selected + 1]
    }
  }
</script>

<template>
  <div class="tabs">
    <div class="tabs__tablist" role="tablist" aria-label="Sample Tabs">
      <button class="tabs__prev button -circle" @click="prev">
        <span class="srOnly">previous</span>
        <svg class="button__icon"><use href="/main-icons-sprite.svg#chevron-left" /></svg>
      </button>
      <button
        class="tabs__button"
        :class="{ '-selected': tab.selected }"
        v-for="tab in tabs"
        role="tab"
        :aria-selected="tab.selected"
        :aria-controls="tab.panelId"
        :id="tab.buttonId"
        ref="tablist"
        :tabindex="tab.tabindex"
        v-text="tab.title"
      ></button>
      <button class="tabs__next button -circle" @click="next">
        <span class="srOnly">next</span>
        <svg class="button__icon"><use href="/main-icons-sprite.svg#chevron-right" /></svg>
      </button>
    </div>

    <div
        class="tabs__panel"
        v-for="tab in tabs"
        role="tabpanel"
        :tabindex="tab.tabindex"
        :id="tab.panelId"
        :aria-labelledby="tab.buttonId"
        :hidden="!tab.selected"
      >
      <slot :name="tab.panelId" />
    </div>
  </div>
</template>
