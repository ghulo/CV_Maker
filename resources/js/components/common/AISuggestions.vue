<template>
  <div class="ai-suggestions">
    <h3>AI Suggestions</h3>
    <div class="suggestions-list">
      <div v-for="suggestion in suggestions" :key="suggestion.id" class="suggestion-item">
        <h4>{{ suggestion.title }}</h4>
        <p>{{ suggestion.description }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AISuggestions',
  data() {
    return {
      suggestions: [
        { id: 1, title: 'Add Skills', description: 'Add more skills to your CV' },
        { id: 2, title: 'Improve Summary', description: 'Make your summary more compelling' }
      ]
    }
  }
}
</script>

<style scoped>
.ai-suggestions {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style> 