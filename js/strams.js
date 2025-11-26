    gsap.registerPlugin(ScrollTrigger);
    
    // Header Fade Up
    gsap.to(".animate-fade-up", {
        y: 0,
        opacity: 1,
        duration: 0.8,
        stagger: 0.2,
        ease: "power2.out"
    });

    // Cards Stagger
    gsap.to(".stream-card", {
        scrollTrigger: {
            trigger: ".stream-card",
            start: "top 85%",
        },
        y: 0,
        opacity: 1,
        duration: 0.8,
        stagger: 0.1,
        ease: "back.out(1.2)"
    });