
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inscription-form</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
       * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0px 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h2 {
            font-size: 28px;
            margin-bottom: 8px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        form {
            padding: 40px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }


select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    background-color: #f9f9f9;
    font-size: 14px;
    color: #333;
    transition: border-color 0.3s, box-shadow 0.3s;
}

select:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0,123,255,0.5);
    outline: none;
}

            
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        button:active {
            transform: translateY(0);
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }
        .icon {
            display: inline-block;
            margin-right: 8px;
            color: #667eea;
        }
        span{
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        span a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #f00;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .success-message {
            background: #efe;
            color: #3c3;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #0f0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
     <div class="header">
            <h2>📝 Inscription</h2>
            <p>Créez votre compte</p>
        </div>
        <?php
        // Afficher le message d'erreur si présent
        if(isset($_GET['error'])){
            echo "<div style='margin: 20px 20px 0; '><div class='error-message'><i class='fas fa-exclamation-circle'></i> ".$_GET['error']."</div></div>";
        }
        ?>
        <form action="../logique/inscri.php" method="POST" onsubmit="return validateForm()">
             <div class="form-group">
                <label for="nom"><i class="fas fa-user icon"></i>Nom d'utilisateur *</label>
                <input type="text" id="nom" name="nom" placeholder="Entrez un nom unique" required><br>
                <small style="color: #999; font-size: 12px;"> Le nom doit être unique</small>
            </div>
             <div class="form-group">
                <label for="prenom"><i class="fas fa-user icon"></i>Prénom *</label>
                <input type="text" id="prenom" name="prenom" placeholder="Entrez votre prénom" required><br>
            </div>
             <div class="form-group">
                <label for="numero"><i class="fas fa-phone icon"></i>Téléphone *</label>
                <input type="tel" id="numero" name="numero" pattern="^\+?[0-9]{8}$" placeholder="Ex: +22690123456" required><br>
            </div>
                <div class="form-group">
                <label for="email"><i class="fas fa-envelope icon"></i>Email *</label>
                <input type="email" id="email" name="email" placeholder="Entrez un email unique" required><br>
                <small style="color: #999; font-size: 12px;"> L'email doit être unique</small>
            </div>
             <div class="form-group">
                <label for="mdp"><i class="fas fa-lock icon"></i>Mot de passe *</label>
                <input type="password" id="mdp" name="mdp" placeholder="Entrez un mot de passe fort" required minlength="6"><br>
                <small style="color: #999; font-size: 12px;"> Le mot de passe doit être unique et min. 6 caractères</small>
            </div>
            <div class="form-group">
                <label for="personalite">Personalité</label>
                <select id="personalite" name="personalite">
                    <option value="">Sélectionnez une personalité</option>
                    <option value="#">client</option>
                    <option value="#">admin</option>
                </select>
            </div>
            <button type="submit" name="register"> S'inscrire</button>
                <span>vous avez déjà un compte? <a href="conexion.php">Connectez-vous</a></span>    
    </form>
    <script>
        function validateForm() {
            const nom = document.getElementById('nom').value.trim();
            const email = document.getElementById('email').value.trim();
            const mdp = document.getElementById('mdp').value.trim();
            
            if (nom.length <=10) {
                alert('❌ Le nom doit avoir au moins 3 caractères');
                return false;
            }
            
            if (!email.includes('@')) {
                alert('❌ Veuillez entrer un email valide');
                return false;
            }
            
            if (mdp.length < 6) {
                alert('❌ Le mot de passe doit avoir au moins 6 caractères');
                return false;
            }
            
            return true;
        }
    </script>
</div>


</body>
</html> 