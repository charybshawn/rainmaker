import { ref, onMounted, onUnmounted } from 'vue'

export function useStarField() {
  const starsCanvas = ref(null)
  let animationId = null
  let staticStars = []
  let shootingStars = []
  let satellites = []
  let satelliteSpawnTimer = 0
  let satelliteSpawnInterval = 2400

  const createStaticStar = (canvas) => {
    const distanceFactor = Math.random()
    const baseOpacity = 0.2 + distanceFactor * 0.6

    return {
      x: Math.random() * canvas.width,
      y: Math.random() * canvas.height,
      size: Math.random() * (distanceFactor * 2) + 0.5,
      opacity: baseOpacity,
      baseOpacity,
      distanceFactor,
      twinkleSpeed: 0.01 + Math.random() * 0.02,
      twinkleDirection: Math.random() < 0.5 ? -1 : 1
    }
  }

  const createShootingStar = () => {
    const distanceFactor = Math.random()
    return {
      x: Math.random() * window.innerWidth + 100,
      y: Math.random() * (window.innerHeight * 0.3),
      size: Math.random() * (distanceFactor * 2) + 1,
      speed: 2 + Math.random() * (distanceFactor * 3),
      opacity: 0.4 + distanceFactor * 0.4,
      distanceFactor,
      tail: [],
      tailLength: 15 + Math.floor(distanceFactor * 10)
    }
  }

  const createSatellite = () => {
    const edge = Math.floor(Math.random() * 4)
    let x, y, vx, vy

    switch (edge) {
      case 0: // Top
        x = Math.random() * window.innerWidth
        y = -50
        vx = (Math.random() - 0.5) * 2
        vy = 0.5 + Math.random() * 1.5
        break
      case 1: // Right
        x = window.innerWidth + 50
        y = Math.random() * window.innerHeight
        vx = -(0.5 + Math.random() * 1.5)
        vy = (Math.random() - 0.5) * 2
        break
      case 2: // Bottom
        x = Math.random() * window.innerWidth
        y = window.innerHeight + 50
        vx = (Math.random() - 0.5) * 2
        vy = -(0.5 + Math.random() * 1.5)
        break
      case 3: // Left
        x = -50
        y = Math.random() * window.innerHeight
        vx = 0.5 + Math.random() * 1.5
        vy = (Math.random() - 0.5) * 2
        break
    }

    return {
      x, y, vx, vy,
      size: 1.5 + Math.random() * 1,
      opacity: 0.6 + Math.random() * 0.3,
      blinkTimer: 0,
      blinkInterval: Math.random() * 60 + 120,
      isBlinking: false
    }
  }

  const createMilkyWayBackground = (ctx, canvas) => {
    const gradient = ctx.createRadialGradient(
      canvas.width * 0.7, canvas.height * 0.3, 0,
      canvas.width * 0.7, canvas.height * 0.3, canvas.width * 0.8
    )
    gradient.addColorStop(0, 'rgba(100, 100, 150, 0.03)')
    gradient.addColorStop(0.3, 'rgba(80, 80, 120, 0.02)')
    gradient.addColorStop(1, 'rgba(30, 30, 60, 0.01)')

    ctx.fillStyle = gradient
    ctx.fillRect(0, 0, canvas.width, canvas.height)
  }

  const initializeStarField = () => {
    if (!starsCanvas.value) return

    const canvas = starsCanvas.value
    const ctx = canvas.getContext('2d')

    const resizeCanvas = () => {
      canvas.width = window.innerWidth
      canvas.height = window.innerHeight

      // Reinitialize stars for new canvas size
      staticStars = Array.from({ length: 200 }, () => createStaticStar(canvas))
      shootingStars = Array.from({ length: 3 }, () => createShootingStar())
      satellites = []
    }

    resizeCanvas()
    window.addEventListener('resize', resizeCanvas)

    const animate = () => {
      ctx.clearRect(0, 0, canvas.width, canvas.height)

      // Draw Milky Way background
      createMilkyWayBackground(ctx, canvas)

      // Draw static stars with depth-based opacity
      staticStars.forEach(star => {
        const finalOpacity = star.opacity * star.distanceFactor
        ctx.globalAlpha = finalOpacity
        ctx.fillStyle = '#ffffff'
        ctx.beginPath()
        ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2)
        ctx.fill()

        // Twinkle effect
        const minOpacity = star.baseOpacity * 0.3
        const maxOpacity = star.baseOpacity * 1.2

        star.opacity += star.twinkleSpeed * star.twinkleDirection
        if (star.opacity <= minOpacity || star.opacity >= maxOpacity) {
          star.twinkleDirection *= -1
        }
      })

      // Draw shooting stars
      shootingStars.forEach((star, index) => {
        star.x -= star.speed
        star.y += star.speed * 0.5

        // Add to tail
        star.tail.unshift({ x: star.x, y: star.y })
        if (star.tail.length > star.tailLength) {
          star.tail.pop()
        }

        // Draw tail
        star.tail.forEach((point, i) => {
          const tailOpacity = (1 - i / star.tailLength) * star.opacity * star.distanceFactor * 0.5
          ctx.globalAlpha = tailOpacity
          ctx.fillStyle = '#ffffff'
          const tailSize = star.size * (1 - i / star.tailLength)
          ctx.beginPath()
          ctx.arc(point.x, point.y, tailSize, 0, Math.PI * 2)
          ctx.fill()
        })

        // Draw main star
        ctx.globalAlpha = star.opacity * star.distanceFactor
        ctx.fillStyle = '#ffffff'
        ctx.beginPath()
        ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2)
        ctx.fill()

        // Add glow effect
        const glowIntensity = star.distanceFactor > 0.7 ? 0.15 : 0.05
        ctx.globalAlpha = star.opacity * glowIntensity
        ctx.fillStyle = '#ffffff'
        ctx.beginPath()
        ctx.arc(star.x, star.y, star.size * (2 + star.distanceFactor), 0, Math.PI * 2)
        ctx.fill()

        // Remove if off screen
        if (star.x < -100) {
          shootingStars.splice(index, 1)
          shootingStars.push(createShootingStar())
        }
      })

      // Handle satellite spawning
      satelliteSpawnTimer++
      if (satelliteSpawnTimer >= satelliteSpawnInterval) {
        satellites.push(createSatellite())
        satelliteSpawnTimer = 0
      }

      // Draw satellites
      satellites.forEach((satellite, index) => {
        satellite.x += satellite.vx
        satellite.y += satellite.vy

        // Handle blinking
        satellite.blinkTimer++
        if (satellite.blinkTimer >= satellite.blinkInterval) {
          satellite.isBlinking = !satellite.isBlinking
          satellite.blinkTimer = 0
          if (!satellite.isBlinking) {
            satellite.blinkInterval = Math.random() * 60 + 120
          } else {
            satellite.blinkInterval = 3
          }
        }

        // Draw satellite
        if (!satellite.isBlinking) {
          ctx.globalAlpha = satellite.opacity
          ctx.fillStyle = '#ffffff'
          ctx.beginPath()
          ctx.arc(satellite.x, satellite.y, satellite.size, 0, Math.PI * 2)
          ctx.fill()

          // Add navigation lights occasionally
          if (Math.random() < 0.2) {
            ctx.globalAlpha = satellite.opacity * 0.6
            ctx.fillStyle = Math.random() < 0.5 ? '#ff4444' : '#44ff44'
            ctx.beginPath()
            ctx.arc(satellite.x + satellite.size * 1.5, satellite.y, satellite.size * 0.3, 0, Math.PI * 2)
            ctx.fill()
          }
        }

        // Remove if off screen
        if (satellite.x < -100 || satellite.x > canvas.width + 100 ||
            satellite.y < -100 || satellite.y > canvas.height + 100) {
          satellites.splice(index, 1)
        }
      })

      ctx.globalAlpha = 1
      animationId = requestAnimationFrame(animate)
    }

    // Start animation after a short delay
    setTimeout(() => {
      animate()
    }, 200)

    return () => {
      window.removeEventListener('resize', resizeCanvas)
      if (animationId) {
        cancelAnimationFrame(animationId)
      }
    }
  }

  onMounted(() => {
    const cleanup = initializeStarField()

    onUnmounted(() => {
      if (cleanup) cleanup()
    })
  })

  return {
    starsCanvas
  }
}