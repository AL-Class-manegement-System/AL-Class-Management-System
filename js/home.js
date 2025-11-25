 gsap.registerPlugin(ScrollTrigger);

        // 1. Navbar Animation (Drops down)
        gsap.from(".glass-nav", {
            duration: 1,
            y: -100,
            opacity: 0,
            ease: "power4.out"
        });

        // 2. Hero Content Animation (Staggered fade up)
        const tl = gsap.timeline({ defaults: { ease: "power3.out" } });

        tl.to(".hero-badge", { y: 0, opacity: 1, duration: 0.8, delay: 0.5 })
          .to(".hero-title", { y: 0, opacity: 1, duration: 0.8 }, "-=0.6")
          .to(".hero-desc", { y: 0, opacity: 1, duration: 0.8 }, "-=0.6")
          .to(".hero-buttons", { y: 0, opacity: 1, duration: 0.8 }, "-=0.6")
          .to(".hero-stats", { y: 0, opacity: 1, duration: 0.8 }, "-=0.6");

        // 3. Hero Image Animation (Fade in + slight scale)
        gsap.to(".hero-img-container", {
            duration: 1.5,
            opacity: 1,
            x: 0,
            scale: 1,
            delay: 0.8,
            ease: "power2.out"
        });

        // 4. Feature Cards Animation (Scroll Trigger)
        gsap.to(".section-header", {
            scrollTrigger: {
                trigger: ".section-header",
                start: "top 80%",
            },
            y: 0,
            opacity: 1,
            duration: 1
        });

        gsap.to(".feature-card", {
            scrollTrigger: {
                trigger: ".feature-card",
                start: "top 80%",
            },
            y: 0,
            opacity: 1,
            duration: 0.8,
            stagger: 0.2, // Cards pop up one after another
            ease: "back.out(1.7)"
        });
