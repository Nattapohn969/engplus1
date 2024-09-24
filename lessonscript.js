document.addEventListener("DOMContentLoaded", function () {
    const videos = document.querySelectorAll("video");
    const testButton = document.querySelector(".btn-test");
    let watchedVideos = 0;

    videos.forEach(video => {
        let lastTime = 0; // ตัวแปรบันทึกตำแหน่งล่าสุดที่เล่น

        // เมื่อวิดีโอจบ
        video.addEventListener("ended", function () {
            watchedVideos++;

            // ถ้าดูวิดีโอทุกตัวจบแล้ว เปิดการใช้งานปุ่มทำแบบทดสอบ
            if (watchedVideos === videos.length) {
                testButton.style.pointerEvents = "auto";
                testButton.style.opacity = "1"; // แสดงปุ่มทำแบบทดสอบ
            }
        });

        // ป้องกันการเลื่อน
        video.addEventListener("seeking", function (e) {
            e.preventDefault(); // ห้ามเลื่อนวิดีโอ
            video.currentTime = lastTime; // กลับไปที่ตำแหน่งล่าสุดที่เล่น
        });

        // บันทึกตำแหน่งล่าสุดที่เล่น
        video.addEventListener("timeupdate", function () {
            lastTime = video.currentTime; // บันทึกตำแหน่งล่าสุดที่เล่น
        });

        // ปิดการทำงานของแถบเลื่อนใน video.js
        if (videojs) {
            const player = videojs(video);
            player.ready(function() {
                const progressControl = player.controlBar.progressControl;

                // ปิดการทำงานของแถบเลื่อน
                progressControl.el().addEventListener('mousedown', function(e) {
                    e.preventDefault(); // ห้ามเลื่อนเมื่อคลิกที่แถบเลื่อน
                });

                // ปิดการเลื่อนในวิดีโอ
                player.on('seeking', function() {
                    player.currentTime(lastTime); // ย้อนกลับไปที่ตำแหน่งล่าสุดที่เล่น
                });

                // ปิดการควบคุมการเล่น
                player.controlBar.seekBar.hide(); // ซ่อนแถบเลื่อน
            });
        }
    });
});
