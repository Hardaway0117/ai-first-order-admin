import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

// 彩蛋：貓咪的瞳孔跟著滑鼠移動
document.addEventListener('mousemove', (event) => {
    document.querySelectorAll('.cat-eye').forEach((eye) => {
        const pupil = eye.querySelector('.cat-pupil');
        const rect = eye.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;
        const angle = Math.atan2(event.clientY - centerY, event.clientX - centerX);
        const distance = Math.min(3, Math.hypot(event.clientX - centerX, event.clientY - centerY) / 40);

        pupil.style.transform = `translate(${Math.cos(angle) * distance}px, ${Math.sin(angle) * distance}px)`;
    });
});
