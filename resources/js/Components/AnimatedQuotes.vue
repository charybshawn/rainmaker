<template>
  <div class="relative overflow-visible h-64 flex items-end justify-center perspective-1000">
    <div
      v-for="(quote, index) in quotes"
      :key="quote.id"
      class="absolute inset-0 flex flex-col items-center justify-end text-center px-4 pointer-events-none"
      :class="getQuoteClasses(index)"
      :style="getQuoteStyles(index)"
    >
      <blockquote class="text-4xl font-medium italic text-white leading-relaxed mb-3 max-w-5xl filter transition-all duration-[30000ms] ease-out relative"
        :class="{
          'blur-sm': index !== currentQuoteIndex,
          'blur-none': index === currentQuoteIndex
        }"
        :style="`font-family: ${quoteFonts[index] || 'Georgia, serif'}; font-style: italic;`"
      >
        <span class="relative z-10 px-4">{{ quote.content }}</span>
      </blockquote>
      <cite class="text-lg font-semibold filter transition-all duration-[30000ms] ease-out"
        :class="[
          {
            'blur-sm': index !== currentQuoteIndex,
            'blur-none': index === currentQuoteIndex
          },
          authorColors[index] || 'text-blue-300'
        ]"
      >
        — {{ quote.author_name }}
      </cite>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const quotes = ref([])
const currentQuoteIndex = ref(0)
const intervalId = ref(null)
const quotePaths = ref([]) // Store randomized paths for each quote
const quoteFonts = ref([]) // Store randomized fonts for each quote
const authorColors = ref([]) // Store randomized colors for each author

const QUOTE_DURATION = 35000 // 35 seconds per quote (30s animation + 5s pause)

// Font options for quotes
const fontOptions = [
  "'Georgia', 'Times New Roman', serif",
  "'Playfair Display', 'Times New Roman', serif",
  "'Crimson Text', 'Times New Roman', serif",
  "'Lora', 'Georgia', serif",
  "'Cormorant Garamond', 'Times New Roman', serif",
  "'EB Garamond', 'Times New Roman', serif",
  "'Libre Baskerville', 'Times New Roman', serif"
]

// Author color options
const authorColorOptions = [
  'text-blue-300',
  'text-purple-300',
  'text-green-300',
  'text-yellow-300',
  'text-pink-300',
  'text-cyan-300',
  'text-indigo-300',
  'text-emerald-300'
]

// Generate random path for each quote
const generateRandomPath = () => {
  return {
    startX: (Math.random() - 0.5) * 400, // Random horizontal start position
    startY: (Math.random() - 0.5) * 200, // Random vertical start position
    midX: (Math.random() - 0.5) * 200,   // Random middle curve point
    midY: (Math.random() - 0.5) * 100,   // Random middle curve point
    rotation: (Math.random() - 0.5) * 60, // Random rotation during travel
  }
}

// Generate random font for each quote
const generateRandomFont = () => {
  return fontOptions[Math.floor(Math.random() * fontOptions.length)]
}

// Generate random color for each author
const generateRandomColor = () => {
  return authorColorOptions[Math.floor(Math.random() * authorColorOptions.length)]
}

// Animation state management
const getQuoteClasses = (index) => {
  const isActive = index === currentQuoteIndex.value

  return {
    'transition-all duration-[30000ms] ease-out': true,
    'opacity-100 scale-100 translate-z-0': isActive,
    'opacity-0 scale-50 translate-z-[-200px]': !isActive,
  }
}

const getQuoteStyles = (index) => {
  const isActive = index === currentQuoteIndex.value
  const path = quotePaths.value[index] || { startX: 0, startY: 0, midX: 0, midY: 0, rotation: 0 }

  if (isActive) {
    return {
      transform: 'translateX(0px) translateY(-20px) translateZ(0px) scale(1) rotateY(0deg) rotateX(0deg)',
      opacity: '1',
      filter: 'blur(0px)',
      transition: 'all 30000ms cubic-bezier(0.23, 1, 0.32, 1)'
    }
  } else {
    return {
      transform: `translateX(${path.startX}px) translateY(${path.startY - 20}px) translateZ(-500px) scale(0.05) rotateY(${path.rotation}deg) rotateX(${path.rotation / 2}deg)`,
      opacity: '0',
      filter: 'blur(20px)',
      transition: 'all 30000ms cubic-bezier(0.23, 1, 0.32, 1)'
    }
  }
}

const fetchQuotes = async () => {
  try {
    const response = await axios.get('/api/quotes')
    quotes.value = response.data

    // Generate random paths, fonts, and colors for each quote
    quotePaths.value = quotes.value.map(() => generateRandomPath())
    quoteFonts.value = quotes.value.map(() => generateRandomFont())
    authorColors.value = quotes.value.map(() => generateRandomColor())

    if (quotes.value.length > 0) {
      startQuoteCycling()
    }
  } catch (error) {
    console.error('Error fetching quotes:', error)
    // Fallback to a default quote if API fails
    quotes.value = [{
      id: 'fallback',
      content: 'The stock market is designed to transfer money from the impatient to the patient.',
      author_name: 'Warren Buffett'
    }]
    quotePaths.value = [generateRandomPath()]
    quoteFonts.value = [generateRandomFont()]
    authorColors.value = [generateRandomColor()]
    startQuoteCycling()
  }
}

const startQuoteCycling = () => {
  if (quotes.value.length <= 1) return

  console.log(`Starting quote cycling with ${quotes.value.length} quotes, ${QUOTE_DURATION}ms interval`)

  intervalId.value = setInterval(() => {
    const nextIndex = (currentQuoteIndex.value + 1) % quotes.value.length
    console.log(`Cycling from quote ${currentQuoteIndex.value} to ${nextIndex}`)
    currentQuoteIndex.value = nextIndex
  }, QUOTE_DURATION)
}

const stopQuoteCycling = () => {
  if (intervalId.value) {
    clearInterval(intervalId.value)
    intervalId.value = null
  }
}

onMounted(() => {
  fetchQuotes()
})

onUnmounted(() => {
  stopQuoteCycling()
})
</script>

<style scoped>
/* 3D Perspective and Space Travel Effects */
.perspective-1000 {
  perspective: 1000px;
  perspective-origin: center center;
}

/* Smooth transitions with cubic bezier for natural motion */
.transition-all {
  transition-property: opacity, transform, filter;
}

/* Custom 3D transform utility classes */
.translate-z-0 {
  transform: translateZ(0px);
}

.translate-z-\[-100px\] {
  transform: translateZ(-100px);
}

.translate-z-\[-200px\] {
  transform: translateZ(-200px);
}

.translate-z-\[-400px\] {
  transform: translateZ(-400px);
}

/* Enhanced blur effects for depth perception */
.filter {
  transition: filter 30000ms cubic-bezier(0.23, 1, 0.32, 1);
}

/* Preserve 3D transformations */
div[style*="translateZ"] {
  transform-style: preserve-3d;
  backface-visibility: hidden;
}
</style>