---
title: SearchResults
description: A reusable component for displaying search results
---

# SearchResults

The SearchResults component is a reusable component that provides a consistent interface for displaying search results across the application. It includes a search field, refresh button, and a list of results.

## Features

- Search field with clear button
- Refresh button to update results
- Loading state indicator
- Empty state message
- List of results with icons and links
- Customizable styling through props

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| containerClass | string | '' | Additional CSS class for the container |
| searchClass | string | '' | Additional CSS class for the search field |
| resultsClass | string | '' | Additional CSS class for the results container |

## Usage

```vue
<template>
  <SearchResults 
    container-class="custom-container"
    search-class="custom-search"
    results-class="custom-results"
  />
</template>

<script setup>
import SearchResults from '../../components/SearchResults.vue'
</script>
```

## Styling

The component uses BEM-style CSS classes that can be overridden or extended:

- `.search-results` - Main container
- `.search-results__header` - Header section containing search and actions
- `.search-results__search` - Search field container
- `.search-results__actions` - Actions container
- `.search-results__list` - Results list container

## Dependencies

- Vue 3
- Nextcloud Vue Components
- Vue Material Design Icons

## Examples

### Basic Usage

```vue
<template>
  <SearchResults />
</template>
```

### With Custom Styling

```vue
<template>
  <SearchResults 
    container-class="my-custom-container"
    search-class="my-custom-search"
    results-class="my-custom-results"
  />
</template>

<style scoped>
.my-custom-container {
  background-color: var(--color-background-hover);
}

.my-custom-search {
  width: 100%;
}

.my-custom-results {
  max-height: 500px;
  overflow-y: auto;
}
</style>
```

## Notes

- The component uses the `objectStore` for state management
- Search results are automatically fetched when the component is mounted
- The component handles loading states and empty states automatically
- Results are displayed as a list of items with titles, summaries, and icons 