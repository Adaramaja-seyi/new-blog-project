<template>
  <div class="font-test">
    <div class="container">
      <h1>Font Test Component</h1>
      <p>This component tests both Inter and Neue Montreal fonts with the new SASS-only setup.</p>
      
      <div class="font-samples">
        <div class="font-sample">
          <h2>Inter Font (Primary)</h2>
          <p class="inter-font">This text uses Inter font - The quick brown fox jumps over the lazy dog.</p>
          <p class="inter-font">ABCDEFGHIJKLMNOPQRSTUVWXYZ</p>
          <p class="inter-font">abcdefghijklmnopqrstuvwxyz</p>
          <p class="inter-font">0123456789</p>
        </div>
        
        <div class="font-sample">
          <h2>Neue Montreal Font (Secondary)</h2>
          <p class="neue-font">This text uses Neue Montreal font - The quick brown fox jumps over the lazy dog.</p>
          <p class="neue-font">ABCDEFGHIJKLMNOPQRSTUVWXYZ</p>
          <p class="neue-font">abcdefghijklmnopqrstuvwxyz</p>
          <p class="neue-font">0123456789</p>
        </div>
      </div>
      
      <div class="css-variables-test">
        <h2>CSS Variables Test</h2>
        <div class="variable-display">
          <div class="variable-item">
            <strong>Primary Color:</strong> 
            <span class="color-swatch" style="background-color: var(--primary-color);"></span>
            <code>var(--primary-color)</code>
          </div>
          <div class="variable-item">
            <strong>Primary Blue:</strong> 
            <span class="color-swatch" style="background-color: var(--primary-blue);"></span>
            <code>var(--primary-blue)</code>
          </div>
          <div class="variable-item">
            <strong>Text Primary:</strong> 
            <span class="color-swatch" style="background-color: var(--text-primary);"></span>
            <code>var(--text-primary)</code>
          </div>
        </div>
      </div>
      
      <div class="button-test">
        <h2>Button Test</h2>
        <div class="button-group">
          <button class="btn btn-primary">Primary Button</button>
          <button class="btn btn-outline-primary">Outline Button</button>
          <button class="btn btn-light">Light Button</button>
        </div>
      </div>
      
      <div class="card-test">
        <h2>Card Test</h2>
        <div class="card">
          <div class="card-header">
            <h3>Test Card</h3>
          </div>
          <div class="card-body">
            <p>This is a test card to verify the styling is working correctly.</p>
            <span class="category-tag">Test Tag</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FontTest'
}
</script>

<style scoped>
.font-test {
  padding: var(--spacing-xl);
  background-color: var(--bg-secondary);
  min-height: 100vh;
}

.font-samples {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--spacing-xl);
  margin: var(--spacing-xl) 0;
}

.font-sample {
  background: var(--bg-primary);
  padding: var(--spacing-lg);
  border-radius: var(--border-radius-lg);
  border: 1px solid var(--border-color);
}

.inter-font {
  font-family: var(--font-family-primary);
  font-size: var(--font-size-base);
  line-height: var(--line-height-base);
}

.neue-font {
  font-family: var(--font-family-secondary);
  font-size: var(--font-size-base);
  line-height: var(--line-height-base);
}

.css-variables-test {
  background: var(--bg-primary);
  padding: var(--spacing-lg);
  border-radius: var(--border-radius-lg);
  border: 1px solid var(--border-color);
  margin-top: var(--spacing-xl);
}

.variable-display {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: var(--spacing-md);
  margin-top: var(--spacing-md);
}

.variable-item {
  display: flex;
  align-items: center;
  gap: var(--spacing-sm);
  padding: var(--spacing-sm);
  background: var(--bg-secondary);
  border-radius: var(--border-radius);
}

.color-swatch {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 1px solid var(--border-color);
}

code {
  font-family: 'Courier New', monospace;
  background: var(--bg-dark);
  color: var(--text-light);
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 0.875rem;
}

.button-test, .card-test {
  background: var(--bg-primary);
  padding: var(--spacing-lg);
  border-radius: var(--border-radius-lg);
  border: 1px solid var(--border-color);
  margin-top: var(--spacing-xl);
}

.button-group {
  display: flex;
  gap: var(--spacing-md);
  flex-wrap: wrap;
}

@media (max-width: 768px) {
  .font-samples {
    grid-template-columns: 1fr;
  }
  
  .variable-display {
    grid-template-columns: 1fr;
  }
  
  .button-group {
    flex-direction: column;
  }
}
</style>
