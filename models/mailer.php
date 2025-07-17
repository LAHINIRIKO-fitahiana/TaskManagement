<?php
session_start(); // Démarre la session

// Inclure le fichier de connexion à la base de données
include '../includes/db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Assurez-vous que le chemin vers l'autoloader de Composer est correct
require '../vendor/autoload.php'; // Chemin vers le dossier vendor généré par Composer

// Création d'une instance PHPMailer
$mail = new PHPMailer(true);

// Activer le débogage SMTP si nécessaire
// $mail->SMTPDebug = SMTP::DEBUG_SERVER;

$mail->isSMTP();
$mail->SMTPAuth = true;

// Configuration du serveur SMTP
$mail->Host = 'smtp.gmail.com';  // Remplacez par votre hôte SMTP
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->Username = 'hsafidisoagilbert@gmail.com';  // Remplacez par votre nom d'utilisateur SMTP
$mail->Password = 'hbwo pprb uzrt mrof';  // Remplacez par votre mot de passe SMTP

$mail->isHTML(true); // Permet l'envoi de contenu HTML

// Assurez-vous que $email contient bien l'email du destinataire
$email = $_POST['email']; // Récupérer l'email du destinataire depuis un formulaire ou une autre source

$mail->setFrom('hsafidisoagilbert@gmail.com', 'srb_Ihorombe');  // L'adresse de l'expéditeur
$mail->addAddress($email);  // Destinataire dynamique

// Sujet de l'email
$mail->Subject = 'Réinitialisation du mot de passe';

// Corps de l'email
$mail->Body = '<h3>Bonjour,</h3>
               <p>Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien ci-dessous :</p>
               <p><a href="http://localhost/gestion_taches/templetes/reset_password?token=' . $token . '">Réinitialiser votre mot de passe</a></p>
               <p>Si vous n\'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>
               <p>Cordialement,</p>
               <p>L\'équipe de gestion des activités.</p>';

try {
    if ($mail->send()) {
        echo 'Message envoyé avec succès.';
    } else {
        echo 'Erreur lors de l\'envoi du message.';
    }
} catch (Exception $e) {
    echo "Erreur : {$mail->ErrorInfo}";
}

?>
