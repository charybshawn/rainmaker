<template>
  <canvas
    ref="starsCanvas"
    class="fixed inset-0 w-full h-full pointer-events-none"
    style="z-index: 1;"
  ></canvas>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const starsCanvas = ref(null)

const initStarsAnimation = () => {
  setTimeout(() => {
    const canvas = starsCanvas.value
    if (!canvas) return

    const ctx = canvas.getContext('2d')
    if (!ctx) return

    // Set canvas size
    canvas.width = window.innerWidth
    canvas.height = window.innerHeight

    // Shooting stars array
    const shootingStars = []
    const staticStars = []
    const satellites = []

    // Create static twinkling stars with depth
    for (let i = 0; i < 50; i++) {
      const size = Math.random() * 2 + 0.5
      const y = Math.random() * canvas.height

      // Calculate distance factor (smaller stars higher up appear more distant)
      const distanceFactor = (size / 2.5) * ((canvas.height - y) / canvas.height)
      const baseOpacity = 0.05 + (distanceFactor * 0.35) // 0.05 to 0.4 range

      staticStars.push({
        x: Math.random() * canvas.width,
        y: y,
        size: size,
        baseOpacity: baseOpacity,
        opacity: baseOpacity,
        twinkleSpeed: Math.random() * 0.02 + 0.01,
        distanceFactor: distanceFactor
      })
    }

    // Create shooting star function
    const createShootingStar = () => {
      const size = Math.random() * 4 + 1
      const y = Math.random() * canvas.height * 0.6

      // Calculate distance factor for shooting stars
      const distanceFactor = (size / 5) * ((canvas.height - y) / canvas.height)
      const baseOpacity = 0.1 + (distanceFactor * 0.3) // 0.1 to 0.4 range

      return {
        x: canvas.width + 100,
        y: y,
        size: size,
        speed: Math.random() * 5 + 3,
        opacity: baseOpacity,
        baseOpacity: baseOpacity,
        distanceFactor: distanceFactor,
        tailLength: Math.random() * 30 + 20
      }
    }

    // Add initial shooting stars
    for (let i = 0; i < 3; i++) {
      shootingStars.push(createShootingStar())
    }

    // Create satellite function
    const createSatellite = () => {
      const startX = -50
      const endX = canvas.width + 50
      const startY = Math.random() * canvas.height * 0.8 + canvas.height * 0.1 // Avoid extreme edges
      const endY = Math.random() * canvas.height * 0.8 + canvas.height * 0.1

      // Calculate direction vector
      const dx = endX - startX
      const dy = endY - startY
      const distance = Math.sqrt(dx * dx + dy * dy)

      return {
        x: startX,
        y: startY,
        targetX: endX,
        targetY: endY,
        speed: Math.random() * 2 + 1,
        size: Math.random() * 1.5 + 0.5,
        opacity: Math.random() * 0.6 + 0.2,
        directionX: dx / distance,
        directionY: dy / distance,
        blinkTimer: 0,
        blinkInterval: Math.random() * 60 + 30 // Random blink pattern
      }
    }

    // Create Milky Way background
    const createMilkyWayBackground = () => {
      const gradient = ctx.createLinearGradient(0, canvas.height * 0.3, canvas.width, canvas.height * 0.7)
      gradient.addColorStop(0, 'rgba(30, 30, 40, 0.02)')
      gradient.addColorStop(0.2, 'rgba(40, 40, 50, 0.05)')
      gradient.addColorStop(0.4, 'rgba(50, 50, 70, 0.08)')
      gradient.addColorStop(0.6, 'rgba(45, 45, 60, 0.06)')
      gradient.addColorStop(0.8, 'rgba(35, 35, 45, 0.04)')
      gradient.addColorStop(1, 'rgba(25, 25, 35, 0.02)')

      ctx.fillStyle = gradient
      ctx.fillRect(0, 0, canvas.width, canvas.height)



      // Reset alpha for other elements
      ctx.globalAlpha = 1
    }

    // Animation function
    const animate = () => {
      // Clear canvas
      ctx.clearRect(0, 0, canvas.width, canvas.height)

      // Draw Milky Way background
      createMilkyWayBackground()

      // Draw static stars with depth-based opacity
      staticStars.forEach(star => {
        // Use distance factor for opacity calculation
        const finalOpacity = star.opacity * star.distanceFactor
        ctx.globalAlpha = finalOpacity
        ctx.fillStyle = '#ffffff'
        ctx.beginPath()
        ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2)
        ctx.fill()

        // Twinkling effect
        star.opacity += Math.sin(Date.now() * star.twinkleSpeed) * 0.1
        if (star.opacity < 0.05) star.opacity = 0.05
        if (star.opacity > star.baseOpacity * 1.5) star.opacity = star.baseOpacity * 1.5
      })

      // Draw shooting stars
      shootingStars.forEach((star, index) => {
        // Update position
        star.x -= star.speed
        star.y += star.speed * 0.5

        // Remove star if it's off screen
        if (star.x < -star.tailLength || star.y > canvas.height + 50) {
          shootingStars.splice(index, 1)
          // Add new shooting star occasionally
          if (Math.random() < 0.002) {
            shootingStars.push(createShootingStar())
          }
          return
        }

        // Draw star tail (streak effect)
        const gradient = ctx.createLinearGradient(
          star.x, star.y,
          star.x + star.tailLength, star.y - star.tailLength * 0.5
        )
        gradient.addColorStop(0, `rgba(255, 255, 255, ${star.opacity})`)
        gradient.addColorStop(0.5, `rgba(255, 255, 255, ${star.opacity * 0.5})`)
        gradient.addColorStop(1, 'rgba(255, 255, 255, 0)')

        ctx.strokeStyle = gradient
        ctx.lineWidth = star.size
        ctx.lineCap = 'round'
        ctx.beginPath()
        ctx.moveTo(star.x, star.y)
        ctx.lineTo(star.x + star.tailLength, star.y - star.tailLength * 0.5)
        ctx.stroke()

        // Draw main star with glow effect
        const glowIntensity = star.distanceFactor > 0.7 ? 0.15 : 0.05 // Brighter glow for closer stars
        ctx.globalAlpha = star.opacity * glowIntensity
        ctx.fillStyle = '#ffffff'
        ctx.beginPath()
        ctx.arc(star.x, star.y, star.size * (2 + star.distanceFactor), 0, Math.PI * 2)
        ctx.fill()

        // Draw core star
        ctx.globalAlpha = star.opacity
        ctx.fillStyle = '#ffffff'
        ctx.beginPath()
        ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2)
        ctx.fill()
      })

      // Draw satellites
      satellites.forEach((satellite, index) => {
        // Update position
        satellite.x += satellite.directionX * satellite.speed
        satellite.y += satellite.directionY * satellite.speed

        // Update blink timer
        satellite.blinkTimer++

        // Calculate blinking effect
        const blinkOpacity = satellite.blinkTimer % satellite.blinkInterval < 5 ?
          satellite.opacity * 0.3 : satellite.opacity

        // Draw satellite
        ctx.globalAlpha = blinkOpacity
        ctx.fillStyle = '#ffcccc' // Slightly red tint for satellites
        ctx.beginPath()
        ctx.arc(satellite.x, satellite.y, satellite.size, 0, Math.PI * 2)
        ctx.fill()

        // Add subtle movement trail
        ctx.globalAlpha = blinkOpacity * 0.3
        ctx.beginPath()
        ctx.arc(satellite.x - satellite.directionX * 3, satellite.y - satellite.directionY * 3, satellite.size * 0.7, 0, Math.PI * 2)
        ctx.fill()

        // Remove satellite if it's off screen
        if (satellite.x < -100 || satellite.x > canvas.width + 100 ||
            satellite.y < -100 || satellite.y > canvas.height + 100) {
          satellites.splice(index, 1)
        }
      })

      ctx.globalAlpha = 1

      // Add new satellites occasionally
      if (Math.random() < 0.001 && satellites.length < 2) {
        satellites.push(createSatellite())
      }

      requestAnimationFrame(animate)
    }

    // Handle window resize
    const handleResize = () => {
      canvas.width = window.innerWidth
      canvas.height = window.innerHeight
    }

    window.addEventListener('resize', handleResize)

    animate()
  }, 200)
}

onMounted(() => {
  initStarsAnimation()
})
</script>

<style scoped>
/* No additional styles needed - canvas styling is inline */
</style>