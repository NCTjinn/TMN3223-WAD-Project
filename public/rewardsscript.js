// Variables for points and target
const currentPoints = 1250;
const targetPoints = 1500;

// Calculate progress percentage
const progressPercent = (currentPoints / targetPoints) * 100;

// Update the progress bar dynamically
document.querySelector('.progress-fill').style.width = `${progressPercent}%`;

// Update the next reward milestone text dynamically
document.querySelector('.sweets-milestone').textContent = `Next Reward: Free Coffee at ${targetPoints} Points`;
