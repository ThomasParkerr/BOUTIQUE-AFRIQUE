console.clear();

gsap.registerPlugin(ScrollTrigger);

const additionalY = { val: 0 };
let additionalYAnim;
let offset = 0;
const cols = gsap.utils.toArray(".col");

cols.forEach((col, i) => {
  const images = Array.from(col.children); // Use `Array.from` for compatibility and clarity

  // DUPLICATE IMAGES FOR LOOP
  images.forEach((image) => {
    const clone = image.cloneNode(true); // Clone the image element
    col.appendChild(clone); // Append the clone to the column
  });

  // SET ANIMATION
  images.forEach((item) => {
    const columnHeight = item.parentElement.clientHeight;
    const direction = i % 2 !== 0 ? "+=" : "-="; // Change direction for odd columns

    gsap.to(item, {
      y: direction + columnHeight / 2, // Use column height for the animation
      duration: 20, // Infinite scroll duration
      repeat: -1, // Infinite repeat
      ease: "none", // Linear animation
      modifiers: {
        y: (y) => {
          if (direction === "+=") {
            offset += additionalY.val;
            y = (parseFloat(y) - offset) % (columnHeight * 0.5); // Calculate the y position for the upward direction
          } else {
            offset += additionalY.val;
            y = (parseFloat(y) + offset) % -(columnHeight * 0.5); // Calculate the y position for the downward direction
          }
          return y;
        },
      },
    });
  });
});

// SCROLL TRIGGER
const imagesScrollerTrigger = ScrollTrigger.create({
  trigger: "section",
  start: "top 50%",
  end: "bottom 50%",
  onUpdate: (self) => {
    const velocity = self.getVelocity(); // Get scroll velocity

    if (velocity !== 0) {
      if (additionalYAnim) additionalYAnim.kill(); // Kill the previous animation if exists

      // Adjust the additionalY value based on the scroll velocity
      additionalY.val = velocity > 0 ? -velocity / 2000 : -velocity / 3000;

      // Animate additionalY back to 0 for smooth stop
      additionalYAnim = gsap.to(additionalY, { val: 0 });
    }
  },
});
