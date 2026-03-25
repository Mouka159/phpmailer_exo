<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>verify-form</title>
    <style>
    *{
    margin: 0;
    padding: 0;
    }
    body{
        box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
    }
    .verification{
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        background-color: #f0f0f0;
      
    }
    .code-inputs{
        display: flex;
        gap: 10px;
        margin: 20px 0;
    }
    .code-inputs input{
        width: 40px;
        height: 40px;
        text-align: center;
        font-size: 18px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    button{
        padding: 10px 20px;
        background-color: #29b954;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    button:hover{
        background-color: #8cba9a;
    }
    h2{
        text-align:center;
    }
    span{
        margin-top: 10px;
        font-family: serif
    }
    </style>
</head>
<body>
    <!-- Bloc de vérification -->
  <div class="verification">
        <h2>Verification</h2>
        <p>Entrer le code envoyé dans email pour valider cette etape :</p>
        <div class="code-inputs">
            <input type="text" maxlength="1">
            <input type="text" maxlength="1">
            <input type="text" maxlength="1">
            <input type="text" maxlength="1">
            <input type="text" maxlength="1">
            <input type="text" maxlength="1">
        </div>
        <button type="submit">VALIDER</button>
        <span id="timer">00:20</span>
   </div>
</body>
</html>
<script>
let timeLeft = 20; // 2 minutes en secondes
const timerElement = document.getElementById('timer');
const countdown = setInterval(() => {
    let minutes = Math.floor(timeLeft / 60);
    let seconds = timeLeft % 60;
    timerElement.textContent = 
        `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
    timeLeft--;
    if (timeLeft < 0) {
        clearInterval(countdown);
        timerElement.textContent = "votre code est expiré";
    }
}, 1000);
</script>
