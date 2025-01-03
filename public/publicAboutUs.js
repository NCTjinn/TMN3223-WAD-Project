// Search bar toggle functionality
document.querySelector('.search-btn').addEventListener('click', function (e) {
    var input = document.querySelector('.search-txt');
    if (input.classList.contains('open')) {
        input.classList.remove('open');
        input.style.width = '0';
        input.style.opacity = '0';
        input.style.visibility = 'hidden';
    } else {
        input.classList.add('open');
        input.style.width = '200px';
        input.style.opacity = '1';
        input.style.visibility = 'visible';
        input.focus();
    }
    e.preventDefault(); // Prevent default link behavior
});

document.addEventListener('click', function (e) {
    var input = document.querySelector('.search-txt');
    if (!input.contains(e.target) && !document.querySelector('.search-btn').contains(e.target)) {
        input.classList.remove('open');
        input.style.width = '0';
        input.style.opacity = '0';
        input.style.visibility = 'hidden';
    }
});

// Select the video and control icons
const video = document.querySelector('.history-video video');
const playPauseIcon = document.querySelector('.play-pause-icon');
const muteIcon = document.querySelector('.mute-icon');

if (video) {
    // Initialize the video to play in a loop
    video.loop = true;

    // Scroll-based autoplay using IntersectionObserver
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    video.play();
                    playPauseIcon.classList.replace('fa-play', 'fa-pause'); // Update icon to "Pause"
                } else {
                    video.pause();
                    playPauseIcon.classList.replace('fa-pause', 'fa-play'); // Update icon to "Play"
                }
            });
        },
        { threshold: 0.5 } // Video plays when 50% of it is visible
    );

    observer.observe(video);

    // Play/Pause control
    playPauseIcon.addEventListener('click', () => {
        if (video.paused) {
            video.play();
            playPauseIcon.classList.replace('fa-play', 'fa-pause');
        } else {
            video.pause();
            playPauseIcon.classList.replace('fa-pause', 'fa-play');
        }
    });

    // Mute/Unmute control
    muteIcon.addEventListener('click', () => {
        if (video.muted) {
            video.muted = false;
            muteIcon.classList.replace('fa-volume-mute', 'fa-volume-up');
        } else {
            video.muted = true;
            muteIcon.classList.replace('fa-volume-up', 'fa-volume-mute');
        }
    });
}
