<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผลลัพธ์แบบทดสอบ</title>
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .header {
            text-align: center;
        }
        .result-message {
            text-align: center;
            font-size: 1.2rem;
            margin: 20px 0;
        }
        .star-rating {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .star {
            font-size: 4rem;
            cursor: pointer;
            color: gold;
            transition: color 0.2s;
            margin: 0 2px;
        }
        .star:hover,
        .star:hover~.star {
            color: lightgray;
        }
        .back-to-lesson {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ผลลัพธ์แบบทดสอบ</h1>
        </div>

        <div class="result-message">
            <p><?php echo $resultMessage; ?></p>
        </div>

        <!-- Star Rating Section -->
        <div class="star-rating">
            <span class="star" data-value="1">&#9733;</span>
            <span class="star" data-value="2">&#9733;</span>
            <span class="star" data-value="3">&#9733;</span>
            <span class="star" data-value="4">&#9733;</span>
            <span class="star" data-value="5">&#9733;</span>
        </div>
        <p id="rating-message"></p>

        <!-- Back to Lesson Button -->
        <div class="back-to-lesson">
            <a href="lesson.php?lessonID=<?php echo $lessonID; ?>" class="btn">กลับไปที่บทเรียน</a>
        </div>
    </div>

    <script>
        document.querySelectorAll('.star').forEach(star => {
            star.addEventListener('click', function () {
                const rating = this.getAttribute('data-value');
                document.getElementById('rating-message').innerText = `คุณให้คะแนน: ${rating} ดาว`;

                // Highlight the selected stars
                document.querySelectorAll('.star').forEach(s => {
                    s.style.color = s.getAttribute('data-value') <= rating ? 'gold' : 'lightgray';
                });

                // AJAX to submit rating
                const lessonID = <?php echo json_encode($lessonID); ?>;
                const userID = <?php echo json_encode($user_ID); ?>; // from session
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "submit_rating.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (this.status === 200) {
                        alert('คะแนนของคุณถูกบันทึกเรียบร้อยแล้ว');
                    }
                };
                xhr.onerror = function () {
                    alert('เกิดข้อผิดพลาดในการบันทึกคะแนน กรุณาลองใหม่อีกครั้ง');
                };
                xhr.send(`lessonID=${lessonID}&user_ID=${userID}&rating=${rating}`);
            });
        });
    </script>
</body>
</html>
