<?php


session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once("php/db.php");

// Ajout d'une variable JS globale pour savoir si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']) ? 'true' : 'false';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Slide - Play</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css" integrity="sha384-NvKbDTEnL+A8F/AA5Tc5kmMLSJHUO868P+lDtTpJIeQdGYaUIuLr4lVGOEA1OcMy" crossorigin="anonymous">
    <script>
        // Variable globale pour savoir si l'utilisateur est connecté
        window.isLoggedIn = <?= $isLoggedIn ?>;
    </script>
        <?php
        $niveau = $_GET["level"] ?? null;
        // $niveau = $_GET["level"] ?? 1;
        // $prec = $niveau < 2 ? 1 : $niveau - 1;
        // $suiv = $niveau + 1;
        // echo "<div><a href='play2.php?level=$prec'>Previous level</a> - <a href='play2.php?level=$niveau'>Current level</a> - <a href='play2.php?level=$suiv'>Next level</a></div>";
    ?>
    <style>

        .hint-dropdown {
            position: absolute;
            top: 44px;
            right:50%;
            transform: translateX(50%);
            border: 1px solid #000E2C;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 8px 0;
            width: 127px;
            z-index: 100;
            display: none;
            background-color:#0A1539;
        }

      .hint-dropdown::after{
        content: '';
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-bottom: 10px solid #0A1539; /* Couleur du fond du dropdown */
        width: 0;
        height: 0;
        z-index: 100;
      }

        .hint-dropdown::before{
        content: '';
        position: absolute;
        top: -11px; /* 1px plus haut pour la bordure */
        left: 50%;
        transform: translateX(-50%);
        border-left: 11px solid transparent;
        border-right: 11px solid transparent;
        border-bottom: 11px solid #000E2C; /* Même couleur que la bordure du dropdown */
        width: 0;
        height: 0;
        z-index: 99;
        }
        .hint-dropdown button {
            display: block;
            padding: 8px 12px;
            text-decoration: none;
            width:100%;
            font-size: 1rem;
            border-radius: 0px;

            
            /* background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%); */
            color: #fff;
            border: none;
            font-family: 'Space Grotesk', Arial, sans-serif;
            cursor: pointer;
            z-index: 1;
            overflow: hidden;
            transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
            letter-spacing: 0.5px;
            background-color: #0A1539
        }
        .hint-dropdown button:hover {
            background:rgb(0, 16, 49);
        }
 
        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .grid {         
            background-color:transparent;
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            grid-template-rows: repeat(10, 1fr);
            gap : 0;
            transition: opacity 0.3s ease-out, transform 0.3s ease-out; /* Ajout de la transition */
            opacity: 1;
        }

        .grid.fade-out {
            opacity: 0;
        }

        .cell {
            background-color: transparent;
            display: flex;
            justify-content: center;
            align-items: center;
            -webkit-user-select: none;
            user-select: none;
            position: relative;
            overflow: hidden;
        }

        /* .cell.wall {
            background-color: #333333;
            gap:0;
        } */

        .cell.wall {
            background-color: transparent; /* Supprime la couleur de fond par défaut */
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            filter: brightness(0.7) saturate(1.3) hue-rotate(20deg)
            /* filter: brightness(0.4) contrast(1) saturate(2); */
        }

        /* Textures spécifiques pour chaque mur */
        /* .cell.wall.wall-1 {
            background-image: url("game/sprites/walls/wall-1.png");
        }
        .cell.wall.wall-2 {
            background-image: url("game/sprites/walls/wall-2.png");
        }
        .cell.wall.wall-3 {
            background-image: url("game/sprites/walls/wall-3.png");
        }
        .cell.wall.wall-4 {
            background-image: url("game/sprites/walls/wall-4.png");
        }
        .cell.wall.wall-5 {
            background-image: url("game/sprites/walls/wall-5.png");
        }
        .cell.wall.wall-6 {
            background-image: url("game/sprites/walls/wall-6.png");
        }
        .cell.wall.wall-7 {
            background-image: url("game/sprites/walls/wall-7.png");
        }
        .cell.wall.wall-8 {
            background-image: url("game/sprites/walls/wall-8.png");
        }
        .cell.wall.wall-9 {
            background-image: url("game/sprites/walls/wall-9.png");
        } */

        /* .cell.wall.wall-1 {
            background-image: url("game/sprites/walls/wall-stone-1.png");
        }
        .cell.wall.wall-2 {
            background-image: url("game/sprites/walls/wall-stone-2.png");
        }
        .cell.wall.wall-3 {
            background-image: url("game/sprites/walls/wall-stone-3.png");
        }
        .cell.wall.wall-4 {
            background-image: url("game/sprites/walls/wall-stone-4.png");
        }
        .cell.wall.wall-5 {
            background-image: url("game/sprites/walls/wall-stone-5.png");
        } */

        /* .cell.wall.wall-1 {
            background-image: url("game/sprites/walls/wall-metal-1.png");
        }
        .cell.wall.wall-2 {
            background-image: url("game/sprites/walls/wall-metal-2.png");
        }
        .cell.wall.wall-3 {
            background-image: url("game/sprites/walls/wall-metal-3.png");
        }
        .cell.wall.wall-4 {
            background-image: url("game/sprites/walls/wall-metal-4.png");
        }
        .cell.wall.wall-5 {
            background-image: url("game/sprites/walls/wall-metal-5.png");
        } */

        .cell.wall.wall-1 {
            background-image: url("game/sprites/walls/wall-blackstone-1.png");
        }
        .cell.wall.wall-2 {
            background-image: url("game/sprites/walls/wall-blackstone-2.png");
        }
        .cell.wall.wall-3 {
            background-image: url("game/sprites/walls/wall-blackstone-3.png");
        }
        .cell.wall.wall-4 {
            background-image: url("game/sprites/walls/wall-blackstone-4.png");
        }
        .cell.wall.wall-5 {
            background-image: url("game/sprites/walls/wall-blackstone-5.png");
        }


@media (max-width: 600px) {
    .grid {
        gap: 0 !important;
        border-spacing: 0 !important;
    }

    .cell {
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }

    .cell.wall {
        background-color: #333333;
    }
}

        .player {
            width: 100%;
            height: 100%;
            position: absolute;
            z-index: 9;
            transition: transform 0.3s none;
            background-image: url("game/sprites/sprite.png");
            background-size: cover;
            background-repeat: no-repeat;
        }

        .player.animating {
            position: fixed;
            z-index: 9;
        }

        .player.falling {
            animation: fallAnimation 0.5s forwards;
        }

        @keyframes fallAnimation {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(0);
                opacity: 0;
            }
        }

        .fill {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transform: scale(0);
            transform-origin: center;
            z-index: 1;
        }

        /* Skins de traînée */

        .cell.path {
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            position: relative;
        }

        .cell.path.gravillons-1 {
            background-image: url("game/sprites/walls/gravillons.png");
        }
        .cell.path.gravillons-2 {
            background-image: url("game/sprites/walls/gravillons-3.png");
        }
        .cell.path.gravillons-3 {
            background-image: url("game/sprites/walls/gravillons-4.png");
        }
        .cell.path.gravillons-4 {
            background-image: url("game/sprites/walls/gravillons-5.png");
        }
        .cell.path.gravillons-5 {
            background-image: url("game/sprites/walls/gravillons-6.png");
        }

        .cell.visited .fill { 
            background-color: pink;
            transform: scale(1);
            display: flex;
            z-index: 2;
        }
        .cell.tp.visited .fill{
            background-color: transparent;
        }

        .controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            justify-content: space-between;
        }

        .controls .logo{
            position:absolute;
            width:100px !important;
            top:0;
            left:50%;
            transform: translateX(-50%);
            margin-top:-22px;
        }

        button {
            font-size: 16px;
            background-color:white; 
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .instructions {
            margin-top: 20px;
            max-width: 400px;
            text-align: center;
            color: #555;
        }
          .tp {
            background-size: cover;
            background-repeat: no-repeat;
            position: relative;
        }

        .tp::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url("game/sprites/teleporter.png");
        background-size: cover;
        background-repeat: no-repeat;
        z-index: 2;
        pointer-events: none;
        }

        .tp::after,
            .cell.tp.visited::after {
            animation: rotate-tp 2s infinite; /* Animation par étapes distinctes */
            transform-origin: center; /* Rotation autour du centre */
            }

@keyframes rotate-tp {
    0% { transform: rotate(0deg); }
    4.165% { transform: rotate(0deg); }
    4.17% { transform: rotate(15deg); }
    8.33% { transform: rotate(15deg); }
    8.34% { transform: rotate(30deg); }
    12.495% { transform: rotate(30deg); }
    12.5% { transform: rotate(45deg); }
    16.665% { transform: rotate(45deg); }
    16.67% { transform: rotate(60deg); }
    20.83% { transform: rotate(60deg); }
    20.84% { transform: rotate(75deg); }
    24.995% { transform: rotate(75deg); }
    25% { transform: rotate(90deg); }
    29.165% { transform: rotate(90deg); }
    29.17% { transform: rotate(105deg); }
    33.33% { transform: rotate(105deg); }
    33.34% { transform: rotate(120deg); }
    37.495% { transform: rotate(120deg); }
    37.5% { transform: rotate(135deg); }
    41.665% { transform: rotate(135deg); }
    41.67% { transform: rotate(150deg); }
    45.83% { transform: rotate(150deg); }
    45.84% { transform: rotate(165deg); }
    49.995% { transform: rotate(165deg); }
    50% { transform: rotate(180deg); }
    54.165% { transform: rotate(180deg); }
    54.17% { transform: rotate(195deg); }
    58.33% { transform: rotate(195deg); }
    58.34% { transform: rotate(210deg); }
    62.495% { transform: rotate(210deg); }
    62.5% { transform: rotate(225deg); }
    66.665% { transform: rotate(225deg); }
    66.67% { transform: rotate(240deg); }
    70.83% { transform: rotate(240deg); }
    70.84% { transform: rotate(255deg); }
    74.995% { transform: rotate(255deg); }
    75% { transform: rotate(270deg); }
    79.165% { transform: rotate(270deg); }
    79.17% { transform: rotate(285deg); }
    83.33% { transform: rotate(285deg); }
    83.34% { transform: rotate(300deg); }
    87.495% { transform: rotate(300deg); }
    87.5% { transform: rotate(315deg); }
    91.665% { transform: rotate(315deg); }
    91.67% { transform: rotate(330deg); }
    95.83% { transform: rotate(330deg); }
    95.84% { transform: rotate(345deg); }
    99.995% { transform: rotate(345deg); }
    100% { transform: rotate(360deg); }
}


        .cell.tp.visited {
            background-size: cover;
            background-repeat: no-repeat;
            background-color: pink;
        }

        .hole {
            background-image: url("game/sprites/lava.png");
        }

        /* Popup de victoire stylé RGB animé */

    #win-message{
        word-break: break-word;
        padding-inline: 10px;
        
    }

    .tool-used-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 15px;
    padding: 8px 15px;
    background-color: rgba(255, 109, 27, 0.1);
    border: 1px solid rgba(255, 109, 27, 0.3);
    border-radius: 20px;
    color: #FF6D1B;
}

.tool-icon {
    width: 20px;
    height: 20px;
    margin-right: 8px;
    vertical-align: middle;
}

.record-not-saved {
    margin-top: 5px;
    font-size: 0.9rem;
    color: #FF6D1B;
    font-style: italic;
}

    #win-popup {
    
    display: none;
    grid-template-rows: auto;
    aspect-ratio: 1/1 !important;
    width: auto;
    max-height: 60vh;
    max-width: 100vw;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.1);
    box-sizing: border-box;
    margin: 0 auto;
    justify-items: center;
    position: relative; 
    transition: none; 
    opacity: 0;

    transition: opacity 0.5s ease-in-out
}

#win-popup.show {
    opacity: 1;
}

.level-btn.locked, .arrow-disabled {
    opacity: 0.7;
    cursor: not-allowed;
    background-color: rgb(143, 148, 160);
    color: #666;
    display: flex; 
    align-items: center;
    justify-content: center;
    
}
.arrow{
    display: flex!important; 
    align-items: center!important;
    justify-content: center!important;
    font-family: 'Space Grotesk';
    position: relative;
    cursor: pointer;
    color: #0A1539 !important;
    background-color: #ddd;
    width: 50%;
    height: 50%;
    text-align: center;
    box-shadow: 0 2px 16px rgba(0,0,0,0.15);
    border-radius: 100px;
    transform: scale(1.2);
}
.arrow:hover{
  border-radius: 100px;
  background-color:#0A1539;
  color: white !important;
  justify-content: center;
  align-items: center;
  transform: scale(1.2);
  transition-duration: 0.15s;
}


.popup-content {
    position: relative;
    width: 100%;
    aspect-ratio: 1/1 !important;
    height: 100%;
    padding: 20px;
    border-radius: 0; /* Supprimer le border-radius */
    background-color: white;
    color: #0A1539; /* Même couleur que les autres menus */
    text-align: center;
    border-radius: 16px;
    border: none; /* Supprimer la bordure */
    margin: 0; /* Supprimer les marges */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    max-width: calc(60vh - 40px);
}


.popup.show {
    opacity: 1;
    display: grid; /* Utiliser grid comme les autres menus */
}
        @keyframes popup-fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }


.popup-content h2 {
    color: #0A1539; /* Même couleur que les autres titres de menu */
    font-size: 1.8rem;
    letter-spacing: 1px;
    margin-bottom: 20px;
}

.popup-content p {
    color: #444;
    font-size: 1.1rem;
    margin-bottom: 30px;
}
.popup-content button {
    width: 70%;
    font-size: 1.2rem;
    border-radius: 8px;
    background: #0A1539;
    color: white;
    border: none;
    padding: 10px 0;
    margin: 5px 0;
    cursor: pointer;
    transition: transform 0.2s ease, background 0.2s ease;
}

.popup-content button:hover {
    transform: scale(1.05);
    background: #1a2b5d;
}
.popup-content .close {
    display: none;
}
.close:hover {
    color: #FF6D1B;
    transform: scale(1.2);
}
.hint-highlight {
    animation: hint-pulse 1s infinite;
    /* box-shadow: inset 0 0 10px 2px rgba(255, 215, 0, 0.7); */
    position: relative;
    z-index: 10 !important;
}

.hint-highlight::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 20;
    background-color: transparent;
    pointer-events: none;
    z-index: 4;
}

.cell.hint-highlight .fill {
    background-color: transparent !important;
    opacity: 0;
    z-index: 5 !important
}

/* Important : priorité sur les styles visited */
.cell.hint-highlight.visited .fill {
    background-color: transparent !important;
    opacity: 0;
}

/* Ajuster le style pour les téléporteurs en surbrillance */
.cell.tp.hint-highlight {
    background-size: cover;
    background-repeat: no-repeat;
}

.difficulty-selection-container {
    background-color: rgba(10, 21, 57, 0.05);
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
}

.difficulty-selection-container h3 {
    color: #0A1539;
    font-size: 1.1rem;
    margin-top: 0;
    margin-bottom: 15px;
    text-align: center;
}

.difficulty-options-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}

.difficulty-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    padding: 10px;
    border-radius: 8px;
    transition: all 0.2s ease;
    text-align: center;
    max-width: 60px;
}

#random-level-menu .difficulty-option{
    padding: 0px 10px;
    /* width: calc(((60vh - 38px) / 5) - 20px) */
}

#random-level-menu .difficulty-preview{
    margin-top: 8px;
}

.difficulty-option:hover {
    background-color: rgba(10, 21, 57, 0.1);
}

.difficulty-option.selected {
    background-color: rgba(10, 21, 57, 0.15);
    box-shadow: 0 0 0 2px #0A1539;
}

.difficulty-preview {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    margin-bottom: 8px;
}

#random-level-menu .home-btn{
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    text-align: center;
    padding: 8px 12px;
    line-height: 1.2;
}

#generate-random-level {
    margin-bottom: 0px !important;
    margin-top: 0px !important;
}

#random-level-menu h2 {
    text-align: center;
    color: #0A1539;
}
.random-level-content {
    overflow-y: auto;
    overflow-x: hidden;
    min-height: 0;
    max-width: calc(60vh - 70px);
    height: 100%;
    position: relative;
    flex: 1;
    display: flex;
    align-items: center;
}

.random-level-content::-webkit-scrollbar{
    width: 0px;
}

#random-level-menu .custom-levels-buttons {
    width: 70%;
    margin: 0 auto;
    display: flex;
    flex-direction: row;
    gap: 10px;
}


.sound-dropdown {
    position: absolute;
    top: 44px;
    right:50%;
    transform: translateX(50%);
    border: 1px solid #000E2C;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    height: 160px;
    width: 60px;
    background-color: #0A1539;
    z-index: 100;
    display: flex;
    text-align: center;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.sound-dropdown::after {
    content: '';
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-bottom: 10px solid #0A1539;
    width: 0;
    height: 0;
    z-index: 100;
}

.sound-dropdown::before {
    content: '';
    position: absolute;
    top: -11px;
    left: 50%;
    transform: translateX(-50%);
    border-left: 11px solid transparent;
    border-right: 11px solid transparent;
    border-bottom: 11px solid #000E2C;
    width: 0;
    height: 0;
    z-index: 99;
}

.loading {
    pointer-events: none; /* Désactiver les interactions pendant le chargement */
    position: relative;
    width: 100%;
    height: 100%;
}

/* Cacher l'image originale quand le bouton est en mode loading */
.loading img {
    visibility: hidden; /* On garde l'espace occupé mais on cache l'image */
}

/* Ajouter le GIF comme background à la place */
.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url("videos/loading.gif") center center no-repeat;
    background-size: 100%; /* Ajuster la taille selon vos besoins */
    z-index: 10;
}

.sound-control {
    position: relative;
}

main#play{
    overflow-x: hidden;
}

.volume-slider {
    position:absolute;
    top:74px;
    left:50%;
    -webkit-appearance: none;
    appearance: none;
    width: 120px; /* Sera la hauteur après rotation */
    height: 5px; /* Sera la largeur après rotation */
    margin: auto;
    background: transparent; /* Important - transparent pour éviter les conflits */
    outline: none;
    transform: translateX(-50%) rotate(270deg);
}

.volume-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    height: 15px;
    width: 15px;
    border-radius: 50%;
    background: #6366f1;
    cursor: pointer;
    margin-top: -5px; /* Centrer le thumb sur la track */
}

.volume-slider::-webkit-slider-runnable-track {
    width: 100%;
    height: 5px;
    cursor: pointer;
    background: linear-gradient(to right, #6366f1 var(--volume-percent, 50%), #444 var(--volume-percent, 50%));
    border-radius: 5px;
}

.volume-slider::-moz-range-track {
    width: 100%;
    height: 5px;
    cursor: pointer;
    background: #444;
    border-radius: 5px;
}

.volume-slider::-moz-range-progress {
    background-color: #6366f1;
    height: 5px;

    border-radius: 5px;
}

.volume-value {
    display: block;
    color: white;
    font-size: 12px;
    margin-top: 6px;
}


#level-menu, #level-menu-2, #level-menu-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: auto 1fr; /* En-tête, contenu, pied */
    aspect-ratio: 1/1 !important;
    width:auto;
    max-height: 60vh;
    max-width: 100vw;
    /* Styles visuels */
    background-color: white;
    border-radius: 16px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.1);
    z-index: 10;
    box-sizing: border-box;
    margin: 0 auto;
    padding: 20px;
}

#level-menu h2, #level-menu-2 h2, #level-menu-3 h2 {
    grid-row: 1/2;
    grid-column: 1/4;
}

#level-menu > div, #level-menu-2 > div, #level-menu-3 > div {
    grid-row: 2/3;
    grid-column: 1/4;
    justify-items: center;
    align-items: center;

}

#level-menu > button{
    grid-row: 3/4;
    grid-column: 1/4;
}

#home-menu {
    /* Même style que level-menu */
    display: grid;
    grid-template-rows: auto;
    aspect-ratio: 1/1 !important;
    width: auto;
    max-height: 60vh;
    max-width: 100vw;
    border-radius: 16px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.1);
    z-index: 10;
    box-sizing: border-box;
    margin: 0 auto;
    padding: 20px;
    justify-items: center;
    background-color:white;
}


#home-menu h2{
    padding-bottom: 10px !important;
    color: #000e2c;
}

@media (max-width: 768px) {

    
    #editor-iframe {
        height: 100% !important;
    }
}

#editor-iframe {
    border: none;
    border-radius: 8px;
    aspect-ratio: 1/1 !important;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    flex: 1;
    min-height: 400px; /* Hauteur minimale */
    max-width: calc(60vh - 38px) !important;
    padding: 20px;
}


.apply-skins-btn{
    margin-inline: auto !important;
}

.home-btn, .apply-skins-btn{
    width: 100%;
    font-size: 1.2rem;
    border-radius: 8px;
    background-color: #000e2c;
    color: white;
    border: none;
    cursor: pointer;
    transition: transform 0.2s ease, background 0.2s ease;
    font-weight: bold;
}

.home-btn:hover, .apply-skins-btn:hover{
    transform: scale(1.05);
    background: #1a2b5d;
}

.btn-disabled {
    opacity: 0.6;
    cursor: not-allowed !important;
    pointer-events: none;
}

.btn-disabled img {
    opacity: 0.6;
    filter: grayscale(1) brightness(0.7);
}

#editor-menu{
    aspect-ratio: 1/1 !important;
    box-shadow: 0 2px 16px rgba(0,0,0,0.1);
    max-width: calc(60vh - 38px);
    width: 100%;
    padding: 0px;

    overflow: hidden;
    justify-items: center;
}

#editor-menu h2{
    padding-bottom: 10px !important;
}

.custom-levels-table-container {
    flex: 1 1 0;
    min-height: 0;
    max-height: 100%;
    overflow-y: auto;
    overflow-x: auto;
    width: 100%;
    box-sizing: border-box;
}
#custom-levels-table {
    width: 100%;
}

.no-solution-marker {
    position: relative;
}

/* Créer la croix rouge avec pseudo-éléments */
.no-solution-marker::before {
    width: 70%;
    height: 4px;
    margin-left: -35%;
    margin-top: -2px;
    transform: rotate(45deg);
}

.no-solution-marker::after {
    width: 70%;
    height: 4px;
    margin-left: -35%;
    margin-top: -2px;
    transform: rotate(-45deg);
}

/* Améliorer la visibilité des croix */
.no-solution-marker::before,
.no-solution-marker::after {
    content: '';
    position: absolute;
    background-color: rgba(255, 0, 0, 0.9); /* Rouge plus vif */
    top: 50%;
    left: 50%;
    transform-origin: center;
    z-index: 20;
    box-shadow: 0 0 3px rgba(0,0,0,0.5); /* Ajouter une ombre pour plus de visibilité */
    animation: flash-error 1s infinite;
}

/* Animation pour faire clignoter les croix */
@keyframes flash-error {
    0% { opacity: 0.6; }
    50% { opacity: 1; }
    100% { opacity: 0.6; }
}

.no-solution-marker::before,
.no-solution-marker::after {
    animation: flash-error 1s infinite;
}


@keyframes hint-pulse {
    0% { border-color: rgba(255, 215, 0, 0.5); background-image: none; }
    50% { border-color: gold; background-color: rgba(255, 215, 0, 0.3); background-image: none; }
    100% { border-color: rgba(255, 215, 0, 0.5); background-image: none; }
}

/* Styles pour le menu des skins */
.skins-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
    overflow-y: auto;
    max-height: calc(60vh - 140px);
    padding: 0 10px;
    width: 100%;
}

.skins-section {
    background-color: rgba(10, 21, 57, 0.05);
    border-radius: 12px;
    padding: 15px;
}

.skins-section h3 {
    color: #0A1539;
    font-size: 1.1rem;
    margin-top: 0;
    margin-bottom: 15px;
    text-align: center;
}

.skins-grid {
    display: grid;
    /* grid-template-columns: repeat(4, 1fr); */
    grid-template-columns: repeat(auto-fit, minmax(60px, 1fr));
    gap: 10px;
}

#random-level-menu .difficulty-options-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    align-items: center;
    max-width: 100%;
}

#random-level-menu .difficulty-option{
    flex: 0 0 auto;
    width: calc(33% - 10px); /* Pour 3 éléments par ligne avec 10px de gap */
    margin-bottom: 10px;
    box-sizing: border-box;
}

#random-level-menu .skins-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(70px, 1fr));
    gap: 10px;
    width: 100%;
    justify-content: center;
}


/* @media (max-width: 600px) {
    .skins-grid {
        grid-template-columns: repeat(2, 1fr);
    }
} */

.skin-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    padding: 10px;
    border-radius: 8px;
    transition: all 0.2s ease;
    text-align: center;
    width: auto; 
    min-width: 60px; 
}

.skins-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(70px, 1fr));
    gap: 10px;
    width: 100%;
}

.fallguys-player {
    background-image: url("game/sprites/fallguys.png"); 
    background-size: cover;
    background-repeat: no-repeat;
    filter: none;
}
.smiley-player {
    background-image: url("game/sprites/smiley.png");
    background-size: cover;
    background-repeat: no-repeat;
    filter: none;
}
.snake-player {
    background-image: url("game/sprites/snake.png");
    background-size: cover;
    background-repeat: no-repeat;
    filter: none;
}
.cat-player {
    background-image: url("game/sprites/cat.png");
    background-size: cover;
    background-repeat: no-repeat;
    filter: none; 
}
.cool-guy{
    background-image: url("game/sprites/cool-guy.png");
    background-size: cover;
    background-repeat: no-repeat;
    filter: none; 
}

.depressed-player {
    background-image: url("game/sprites/depressed.png");
    background-size: cover;
    background-repeat: no-repeat;
    filter: none;
}

.kirby-player {
    background-image: url("game/sprites/kirby.png");
    background-size: cover;
    background-repeat: no-repeat;
    filter: none;
}

.lucky-block-player {
    background-image: url("game/sprites/lucky-block.png");
    background-size: cover;
    background-repeat: no-repeat;
    filter: none;
}

.skull-player {
    background-image: url("game/sprites/skull.png");
    background-size: cover;
    background-repeat: no-repeat;
    filter: none;
}
.toad-player {
    background-image: url("game/sprites/toad.png");
    background-size: cover;
    background-repeat: no-repeat;
    filter: none; /* Pas de filtres pour garder les couleurs originales */
}


.skin-option:hover {
    background-color: rgba(10, 21, 57, 0.1);
}

.skin-option.selected {
    /* background-color: rgba(10, 21, 57, 0.15); */
    box-shadow: 0 0 0 2px #0A1539;
    margin-inline: -5px;
}

.skin-preview {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    margin-bottom: 8px;
}

.player-preview {
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
}

.default-player {
    background-image: url("game/sprites/sprite.png");
}

.blue-player {
    background-image: url("game/sprites/sprite.png");
    filter: hue-rotate(240deg);
}

.red-player {
    background-image: url("game/sprites/sprite.png");
    filter: hue-rotate(320deg) saturate(1.5);
}

.green-player {
    background-image: url("game/sprites/sprite.png");
    filter: hue-rotate(120deg);
}

.yellow-player {
    background-image: url("game/sprites/sprite.png");
    filter: hue-rotate(150deg) saturate(20);
}

.cyan-player {
    background-image: url("game/sprites/sprite.png");
    filter: hue-rotate(-90deg) saturate(7) ;
}

.orange-player {
    background-image: url("game/sprites/sprite.png");
    filter: hue-rotate(120deg) saturate(2);
}

.pink-player {
    background-image: url("game/sprites/sprite.png");
    filter: hue-rotate(25deg) saturate(2.2) brightness(1.1);
}

.trail-preview {
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.creeper-player {
    background-image: url("game/sprites/creeper.png");
    background-size: cover;
    background-repeat: no-repeat;
    filter: none; /* Pas de filtres pour garder les couleurs originales */
}



/* .apply-skins-btn {
    margin-top: 20px;
    padding: 10px 20px;
} */
 .menu-icon.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
    </style>
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

    <audio id="background-music" loop>
        <source src="audio/background.mp3" type="audio/mpeg">
    </audio>

    <div class="menu-icon" id="menu-icon-btn">
    <img src="icons/trophy.png">
    </div>
    <header>
        <?php include 'php/header1.php'; ?> 
    </header>
    <main id="play">
        <div class="game-wrapper">
            <div class="game-container">
                <div class="controls">
                    <div class="left-wrapper">
                   
                    <a id="home-logo">
                        <img src="icons/logo_home_blue.png" alt="Home">
                    </a>
                    <button id="new-game"><img src="icons/recharger.png" alt="Restart"></button>
                    </div>
                    <div class="title"></div>
    

                    <div class="right-wrapper">
                    <form method="POST" action="game/scripts/solveur.php" id="hint-form">
                        <!-- <input type="hidden" name="level_name" value="<//?= $_GET["level"] ?? 1 ?>"> -->
                        <input type="hidden" name="current_state" id="currentState">
                        <input type="hidden" name="statesaved" id="statesaved">
                        <input type="hidden" name="mode" id="hint-mode" value="">
                        <button type="button" class="hint" id="hint-btn">
                            <img src="icons/point-dinterrogation.png" alt="Hint">
                        </button>
                        <div class="hint-dropdown" id="hint-dropdown" style="display:none;">
                            <button type="button" id="run-solution">Solution</button>
                            <button type="button" id="hint-only-btn">Hint</button>
                        </div>
                    </form>

                    <div class="sound-control">
                        <button type="button" class="sound-btn" id="sound-btn">
                            <img class="sound" src="icons/sound.png" alt="Sound">
                        </button>
                        <div class="sound-dropdown" id="sound-dropdown" style="display:none;">
                                <input type="range" min="0" max="100" value="50" class="volume-slider" id="volume-slider">
                        </div>
                    </div>

                    </div>

                </div>
                <div id="grid" class="grid">
                
                </div>

        <div id="win-popup" class="popup" style="display:none;">
            <div class="popup-content">
                <span id="close-popup" class="close">&times;</span>
                <p id="win-message"></p>
                <!-- <button onclick="goToCampaign()">Campaign</button>
                <button onclick="goToNextLevel()">Next Level</button> -->
            </div>
        </div>

        <div id="skins-menu" style="display: none;">
            <div class="skins-content">
        <div class="skins-section">
            <h3>Character</h3>
            <div class="skins-grid character-skins">
                <div class="skin-option player-skin selected" data-skin="default">
                    <div class="skin-preview player-preview default-player"></div>
                    <span>Purple</span>
                </div>
                <div class="skin-option player-skin" data-skin="fallguys">
                    <div class="skin-preview player-preview fallguys-player"></div>
                    <span>Fall Guys</span>
                </div>
                <div class="skin-option player-skin" data-skin="toad">
                    <div class="skin-preview player-preview toad-player"></div>
                    <span>Toad</span>
                </div>
                <div class="skin-option player-skin" data-skin="cat">
                    <div class="skin-preview player-preview cat-player"></div>
                    <span>Cat</span>
                </div>
                <div class="skin-option player-skin" data-skin="depressed">
                    <div class="skin-preview player-preview depressed-player"></div>
                    <span>Depressed</span>
                </div>
                <div class="skin-option player-skin" data-skin="kirby">
                    <div class="skin-preview player-preview kirby-player"></div>
                    <span>Kirby</span>
                </div>
                <div class="skin-option player-skin" data-skin="lucky-block">
                    <div class="skin-preview player-preview lucky-block-player"></div>
                    <span>Lucky Block</span>
                </div>
                <div class="skin-option player-skin" data-skin="skull">
                    <div class="skin-preview player-preview skull-player"></div>
                    <span>Skull</span>
                </div>
                <div class="skin-option player-skin" data-skin="cool-guy">
                    <div class="skin-preview player-preview cool-guy"></div>
                    <span>Cool Guy</span>
                </div>
                <div class="skin-option player-skin" data-skin="snake">
                    <div class="skin-preview player-preview snake-player"></div>
                    <span>Snake</span>
                </div>
                <div class="skin-option player-skin" data-skin="smiley">
                    <div class="skin-preview player-preview smiley-player"></div>
                    <span>Smiley</span>
                </div>
            </div>
        </div>
        
        <div class="skins-section">
            <h3>Trail</h3>
            <div class="skins-grid trail-skins">
                <div class="skin-option trail-skin selected" data-color="pink">
                    <div class="skin-preview trail-preview" style="background-color: pink;"></div>
                    <span>Pink</span>
                </div>
                <div class="skin-option trail-skin" data-color="#51cf66">
                    <div class="skin-preview trail-preview" style="background-color: #51cf66;"></div>
                    <span>Green</span>
                </div>
                <div class="skin-option trail-skin" data-color="#4D8AFF">
                    <div class="skin-preview trail-preview" style="background-color: #4D8AFF;"></div>
                    <span>Blue</span>
                </div>
                <div class="skin-option trail-skin" data-color="#FF6D1B">
                    <div class="skin-preview trail-preview" style="background-color: #FF6D1B;"></div>
                    <span>Orange</span>
                </div>
                <!-- Nouvelles options de couleur -->
                <div class="skin-option trail-skin" data-color="#9b59b6">
                    <div class="skin-preview trail-preview" style="background-color: #9b59b6;"></div>
                    <span>Purple</span>
                </div>
                <div class="skin-option trail-skin" data-color="#f1c40f">
                    <div class="skin-preview trail-preview" style="background-color: #f1c40f;"></div>
                    <span>Yellow</span>
                </div>
                <div class="skin-option trail-skin" data-color="#1abc9c">
                    <div class="skin-preview trail-preview" style="background-color: #1abc9c;"></div>
                    <span>Turquoise</span>
                </div>
                <div class="skin-option trail-skin" data-color="#adff2f">
                    <div class="skin-preview trail-preview" style="background-color: #adff2f;"></div>
                    <span>Lime</span>
                </div>
                <div class="skin-option trail-skin" data-color="#FF0000">
                    <div class="skin-preview trail-preview" style="background-color: #FF0000;"></div>
                    <span>Red</span>
                </div>
                <div class="skin-option trail-skin" data-color="#964B00">
                    <div class="skin-preview trail-preview" style="background-color: #964B00;"></div>
                    <span>Brown</span>
                </div>
                <div class="skin-option trail-skin" data-color="image:test-tp">
                    <div class="skin-preview trail-preview" style="background-image: url('game/sprites/test-tp.png'); background-size: cover; background-color: transparent;"></div>
                    <span>Nether</span>
                </div>
                <div class="skin-option trail-skin" data-color="image:etoiles">
                    <div class="skin-preview trail-preview" style="background-image: url('game/sprites/etoiles.png'); background-size: cover; background-color: transparent;"></div>
                    <span>Stars</span>
                </div>
                <div class="skin-option trail-skin" data-color="image:water">
                    <div class="skin-preview trail-preview" style="background-image: url('game/sprites/water.png'); background-size: cover; background-color: transparent;"></div>
                    <span>Water</span>
                </div>
                <div class="skin-option trail-skin" data-color="image:grass">
                    <div class="skin-preview trail-preview" style="background-image: url('game/sprites/grass.png'); background-size: cover; background-color: transparent;"></div>
                    <span>Grass</span>
                </div>
                <div class="skin-option trail-skin" data-color="image:rainbow">
                    <div class="skin-preview trail-preview" style="background-image: url('game/sprites/rainbow.png'); background-size: cover; background-color: transparent;"></div>
                    <span>Rainbow</span>
                </div>
                <div class="skin-option trail-skin" data-color="image:particles">
                    <div class="skin-preview trail-preview" style="background-image: url('game/sprites/particles.png'); background-size: cover; background-color: transparent;"></div>
                    <span>Particles</span>
                </div>
                <div class="skin-option trail-skin" data-color="image:coal">
                    <div class="skin-preview trail-preview" style="background-image: url('game/sprites/coal.png'); background-size: cover; background-color: transparent;"></div>
                    <span>Coal</span>
                </div>
                <div class="skin-option trail-skin" data-color="image:ice">
                    <div class="skin-preview trail-preview" style="background-image: url('game/sprites/ice.png'); background-size: cover; background-color: transparent;"></div>
                    <span>Ice</span>
                </div>
                <div class="skin-option trail-skin" data-color="image:turkish-carpet">
                    <div class="skin-preview trail-preview" style="background-image: url('game/sprites/turkish-carpet.png'); background-size: cover; background-color: transparent;"></div>
                    <span>Turkish Carpet</span>
                </div>
                <div class="skin-option trail-skin" data-color="image:cookie">
                    <div class="skin-preview trail-preview" style="background-image: url('game/sprites/cookie.png'); background-size: cover; background-color: transparent;"></div>
                    <span>Cookie</span>
                </div>
                <div class="skin-option trail-skin" data-color="image:lego">
                    <div class="skin-preview trail-preview" style="background-image: url('game/sprites/lego.png'); background-size: cover; background-color: transparent;"></div>
                    <span>Lego</span>
                </div>
                <div class="skin-option trail-skin" data-color="image:chocolate">
                    <div class="skin-preview trail-preview" style="background-image: url('game/sprites/chocolate.png'); background-size: cover; background-color: transparent;"></div>
                    <span>Chocolate</span>
                </div>
                <div class="skin-option trail-skin" data-color="image:scales">
                    <div class="skin-preview trail-preview" style="background-image: url('game/sprites/scales.png'); background-size: cover; background-color: transparent;"></div>
                    <span>Scales</span>
                </div>
                <div class="skin-option trail-skin" data-color="image:sunflower">
                    <div class="skin-preview trail-preview" style="background-image: url('game/sprites/sunflower.png'); background-size: cover; background-color: transparent;"></div>
                    <span>Sunflower</span>
                </div>
            </div>
        </div>
    </div>
    <div class="custom-levels-buttons">
    <button class="home-btn apply-skins-btn">Apply</button>
    </div>
</div>

                <div id="level-menu" style="display: none;">
            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:10px;">
                <button class="level-btn unlocked" data-level="1">1</button>
                <button class="level-btn locked" data-level="2">2 🔒</button>
                <button class="level-btn locked" data-level="3">3 🔒</button>
                <button class="level-btn locked" data-level="4">4 🔒</button>
                <button class="level-btn locked" data-level="5">5 🔒</button>
                <button class="level-btn locked" data-level="6">6 🔒</button>
                <button class="level-btn locked" data-level="7">7 🔒</button>
                <button class="level-btn locked" data-level="8">8 🔒</button>
                <button class="level-btn locked" data-level="9">9 🔒</button>
                <button class="arrow-disabled">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </button>
                <button class="level-btn locked" data-level="10">10 🔒</button>
                <button class="arrow" id="arrow-right-1">
                    <ion-icon name="chevron-forward-outline"></ion-icon>
                </button>
            </div>
        </div>

        <!-- Menu des niveaux 11-20 -->
        <div id="level-menu-2" style="display: none;">
            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:10px;">
                <!-- Ligne 1 -->
                <button class="level-btn locked" data-level="11">11 🔒</button>
                <button class="level-btn locked" data-level="12">12 🔒</button>
                <button class="level-btn locked" data-level="13">13 🔒</button>
                <!-- Ligne 2 -->
                <button class="level-btn locked" data-level="14">14 🔒</button>
                <button class="level-btn locked" data-level="15">15 🔒</button>
                <button class="level-btn locked" data-level="16">16 🔒</button>
                <!-- Ligne 3 -->
                <button class="level-btn locked" data-level="17">17 🔒</button>
                <button class="level-btn locked" data-level="18">18 🔒</button>
                <button class="level-btn locked" data-level="19">19 🔒</button>
                <!-- Ligne 4 : Navigation -->
                <button class="arrow" id="arrow-left-2">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </button>
                <button class="level-btn locked" data-level="20">20 🔒</button>
                <button class="arrow" id="arrow-right-2">
                    <ion-icon name="chevron-forward-outline"></ion-icon>
                </button>
            </div>
        </div>

        <!-- Menu des niveaux 21-30 -->
        <div id="level-menu-3" style="display: none;">
            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:10px;">
                <button class="level-btn locked" data-level="21">21 🔒</button>
                <button class="level-btn locked" data-level="22">22 🔒</button>
                <button class="level-btn locked" data-level="23">23 🔒</button>
                <button class="level-btn locked" data-level="24">24 🔒</button>
                <button class="level-btn locked" data-level="25">25 🔒</button>
                <button class="level-btn locked" data-level="26">26 🔒</button>
                <button class="level-btn locked" data-level="27">27 🔒</button>
                <button class="level-btn locked" data-level="28">28 🔒</button>
                <button class="level-btn locked" data-level="29">29 🔒</button>
                <button class="arrow" id="arrow-left-3">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </button>
                <button class="level-btn locked" data-level="30">30 🔒</button>
                <button class="arrow-disabled">
                    <ion-icon name="chevron-forward-outline"></ion-icon>
                </button>
            </div>
        </div>

        <div id="home-menu" style="display: none;">
            <div style="display:grid; grid-template-columns:1fr; gap:20px; width:70%; margin:0 auto;">
                <button id="campaign" class="home-btn">Campaign</button>
                <button id="custom-levels" class="home-btn">Custom Levels</button>
                <button id="editor" class="home-btn">Editor</button>
                <button id="skins" class="home-btn">Skins</button>
            </div>
        </div>

        <div id="editor-menu" style="display: none;">
                <iframe 
            id="editor-iframe" 
            src="game/html/editor.php" 
            style="
                width: 100%;
                border: none;
                border-radius: 16px;
                background: white;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            "
            loading="lazy">
        </iframe>
        </div>
        <div id="editor-choice-menu" style="display: none;">
            <div style="display:grid; grid-template-columns:1fr; gap:20px; width:70%; margin:0 auto;">
                <button id="random-generator" class="home-btn">Random Generation</button>
                <button id="manual-editor" class="home-btn">Manual Editor</button>
            </div>
        </div>

        <!-- Tableau des niveaux personnalisés -->
<div id="custom-levels-menu" style="display: none;">
    <div class="custom-levels-table-container">
        <table id="custom-levels-table">
            <thead>
                <tr>
                    <th class="table-header-first">Name</th>
                    <th class="table-header">Creator</th>
                    <th class="table-header">Difficulty</th>
                    <th class="table-header-last">Play</th>
                </tr>
            </thead>
            <tbody id="custom-levels-tbody">
    <!-- Les lignes seront générées dynamiquement -->
        </tbody>
        </table>
    </div>
        <!-- Boutons d'action -->
        <div class="custom-levels-buttons">
            <button id="my-levels" class="home-btn">My Levels</button>
        </div>
    </div>

        <!-- Menu My Levels -->
            
    <div id="my-levels-menu" style="display: none;">
        <div class="custom-levels-table-container">
            <table id="my-levels-table">
                <thead>
                    <tr>
                        <th class="table-header-first">Name</th>
                        <th class="table-header">Difficulty</th>
                        <th class="table-header-last">Play</th>
                    </tr>
                </thead>
                <tbody id="my-levels-tbody">
                    <!-- Les données seront chargées dynamiquement -->
                </tbody>
            </table>
        </div>
        <!-- Boutons d'action -->
        <div class="custom-levels-buttons">
            <button id="back-to-custom-levels" class="home-btn">Custom Levels</button>
        </div>
    </div>

        <!-- Nouveau menu Random Level -->
<div id="random-level-menu" style="display: none;">
    <div class="random-level-content">
        <div class="difficulty-selection-container">
            <h3>Select Difficulty</h3>
            <form id="difficulty-form" class="difficulty-options-grid">
                <label class="difficulty-option selected">
                    <input type="radio" name="difficulty" value="0" checked class="difficulty-radio">
                    <div class="difficulty-preview" style="background-image: url('game/sprites/easy.png'); background-size: cover; background-repeat: no-repeat;"></div>
                </label>
                <label class="difficulty-option">
                    <input type="radio" name="difficulty" value="1" class="difficulty-radio">
                    <div class="difficulty-preview" style="background-image: url('game/sprites/easy2.png'); background-size: cover; background-repeat: no-repeat;"></div>

                </label>
                <label class="difficulty-option">
                    <input type="radio" name="difficulty" value="2" class="difficulty-radio">
                    <div class="difficulty-preview" style="background-image: url('game/sprites/medium.png'); background-size: cover; background-repeat: no-repeat;"></div>

                </label>
                <label class="difficulty-option">
                    <input type="radio" name="difficulty" value="3" class="difficulty-radio">
                    <div class="difficulty-preview" style="background-image: url('game/sprites/hard.png'); background-size: cover; background-repeat: no-repeat;"></div>

                </label>
                <label class="difficulty-option">
                    <input type="radio" name="difficulty" value="4" class="difficulty-radio">
                    <div class="difficulty-preview" style="background-image: url('game/sprites/insane.png'); background-size: cover; background-repeat: no-repeat;"></div>
                </label>
            </form>
        </div>
    </div>
    <div class="custom-levels-buttons">
        <button id="generate-random-level" class="home-btn">Generate</button>
        <button id="back-to-editor-choice-menu" class="home-btn">Back to Editor</button>
    </div>
</div>

</div>




    <?php
        if (isset($_SESSION["solution"])): ?>
            <script>
                sessionStorage.setItem("solution", "<?= htmlspecialchars($_SESSION["solution"]) ?>");
            </script>
        <?php 
        unset($_SESSION["solution"]);
        endif;
        ?>
            </div>
                        
        <?php
            if (isset($_GET['level'])) {
    $level = $_GET['level'];
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT moves, users.username FROM stats JOIN users ON stats.user_id = users.id WHERE level_id = ? ORDER BY moves ASC");
        $stmt->execute([$level]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>
    <div class="dropdown<?php echo (empty($results)) ? ' empty' : ''; ?>" id="leaderboard">
        <table>
            <thead>
                <?php if(!empty($results)){
                    echo'<tr><th>Rank</th><th>Name</th><th>Moves</th></tr>';
                } ?>
            </thead>
            <tbody>
                <?php
                $rank = 1;
                foreach ($results as $row) {
                    $isCurrentUser = isset($_SESSION['username']) && $_SESSION['username'] === $row['username'];
                    echo "<tr" . ($isCurrentUser ? " class='highlight'" : "") . ">";
                    echo "<td>{$rank}</td>";
                    echo "<td>{$row['username']}</td>"; 
                    echo "<td>{$row['moves']}</td>";
                    echo "</tr>";
                    $rank++;
                }
                if(empty($results)) {
                    echo "<tr><td colspan='3'>No data</td></tr>";
                }
                ?>
            </tbody>
        </table>   
    </div>
<?php
} // Fin de la condition if (isset($_GET['level']))
?>
        </tbody>
    </table>   
</div>
        </div>


        
<script>
    
    function loadMyLevels() {
    fetch('php/get_my_levels.php')
        .then(res => res.json())
        .then(levels => {
            const tbody = document.querySelector('#my-levels-table tbody');
            tbody.innerHTML = '';
            
            if (!levels.length) {
                tbody.innerHTML = '<tr><td colspan="3">You haven\'t created any levels yet</td></tr>';
                return;
            }
            
            levels.forEach(level => {
                const tr = document.createElement('tr');
                tr.className = 'table-row';
                tr.innerHTML = `
                    <td class="level-name">${level.name}</td>
                    <td class="difficulty">${getDifficultyLabel(level.minmoves)}</td>
                    <td class="actions">
                        <button class="play-level-btn" data-level="${level.id}">Play</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            // Ajoute l'action sur les boutons Play
            tbody.querySelectorAll('.play-level-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    window.location.href = `play.php?level=${this.dataset.level}`;
                });
            });
        })
        .catch(error => {
            // console.error('Error loading personal levels:', error);
            const tbody = document.querySelector('#my-levels-table tbody');
            tbody.innerHTML = '<tr><td colspan="3">Error loading levels</td></tr>';
        });
}

    document.addEventListener('click', (e) => {
    // Ne pas fermer les menus si on clique sur un bouton qui ouvre un menu
    if (e.target.closest('#sound-btn') || 
        e.target.closest('#hint-btn') || 
        e.target.closest('#home-logo')) {
        return;
    }
    
    // Ne pas fermer un menu si on clique à l'intérieur de celui-ci
    if (e.target.closest('#sound-dropdown') || 
        e.target.closest('#hint-dropdown') || 
        e.target.closest('#level-menu') ||
        e.target.closest('#home-menu')) {
        return;
    }
    
    // Si on clique n'importe où ailleurs, fermer tous les menus dropdown
    DropdownMenu.closeAll();

});

    const menuTitles = {
    'level-menu': 'Levels 1-10',
    'level-menu-2': 'Levels 11-20',
    'level-menu-3': 'Levels 21-30',
    'home-menu': 'Main Menu',
    'editor-menu': 'Editor',
    'editor-choice-menu': 'Editor',
    'custom-levels-menu': 'Custom Levels',
    'skins-menu': 'Skins',
    'my-levels-menu': 'My Levels',
    'random-level-menu': 'Random Level',
    'win-popup': 'Congratulations!'
};

    // Gestionnaire de sous-menus
    const MenuManager = {
    // Liste de tous les IDs de menus à gérer
    menuIds: ['level-menu', 'level-menu-2', 'level-menu-3', 'home-menu', 'win-popup', 'editor-menu', 'editor-choice-menu', 'custom-levels-menu', 'skins-menu', 'my-levels-menu', 'random-level-menu'], // Ajout du win-popup

    // Cache tous les menus et la grille
    hideAllMenus: function() {
        this.menuIds.forEach(id => {
            const menu = document.getElementById(id);
            if (menu) menu.style.display = 'none';
        });
    },

    // Affiche un menu spécifique
showMenu: function(menuId) {
    // D'abord, cache tous les menus
    this.hideAllMenus();
    if (menuId === 'home-menu') {
        // Utilisez AJAX pour indiquer au serveur que l'utilisateur est passé par l'accueil
        fetch('php/set_home_flag.php', {
            method: 'POST',
            credentials: 'same-origin'
        });
    }
    
    // Cache la grille de jeu
    const grid = document.getElementById('grid');
    if (grid) grid.style.display = 'none';
    
    // Affiche le menu demandé
    const menuToShow = document.getElementById(menuId);
    if (menuToShow) menuToShow.style.display = 'grid';
    
    // MODIFICATION : Déterminer le titre selon le menu affiché
    const headerTitle = document.querySelector('.controls .title');
    if (headerTitle) {
        if (['level-menu', 'level-menu-2', 'level-menu-3'].includes(menuId)) {
            // Pour les menus de niveaux, utiliser TOUJOURS le titre du menu
            headerTitle.textContent = menuTitles[menuId];
        } else {
            // Pour tous les autres menus, utiliser le titre du menu
            headerTitle.textContent = menuTitles[menuId] || '';
        }
    }
    
    // Mettre à jour l'état du bouton hint
    updateButtonState();
    
    // Mettre à jour l'état du bouton leaderboard
    updateLeaderboardButtonState();
    
    // console.log(`Menu affiché : ${menuId}`);
},
    
    // Ferme tous les menus et affiche la grille
closeMenus: function() {
    this.hideAllMenus();
    
    // Réaffiche la grille
    const grid = document.getElementById('grid');
    if (grid) grid.style.display = '';

    const headerTitle = document.querySelector('.controls .title');
    if (headerTitle) {
        const levelName = sessionStorage.getItem("levelName");
        if (levelName) {
            headerTitle.textContent = levelName;
        } else {
            // Fallback avec le numéro de niveau depuis l'URL
            const urlParams = new URLSearchParams(window.location.search);
            
            if (urlParams.has('level')) {
                // Si un niveau est spécifié dans l'URL
                const levelNumber = urlParams.get('level');
                headerTitle.textContent = `Level ${levelNumber}`;
            } else {
                // Si aucun niveau n'est spécifié, garder le titre actuel (ne pas le changer)
                // Ne rien faire - laisser le titre tel qu'il est
            }
        }
    }
    
    // Mettre à jour l'état du bouton hint
    updateButtonState();
    
    // Mettre à jour l'état du bouton leaderboard
    updateLeaderboardButtonState();
}
};

function updateLeaderboardButtonState() {
    const leaderboardButton = document.getElementById('menu-icon-btn');
    const urlParams = new URLSearchParams(window.location.search);
    
    if (leaderboardButton) {
        if (urlParams.has('level') && isGameActive()) {
            // Si on est dans un niveau, activer le bouton
            leaderboardButton.classList.remove('disabled');
            leaderboardButton.style.pointerEvents = '';
        } else {
            // Si on n'est pas dans un niveau, désactiver le bouton
            leaderboardButton.classList.add('disabled');
            leaderboardButton.style.pointerEvents = 'none';
        }
    }
}

        
        // document.getElementById('campaign').addEventListener('click', function() {
        //     // Cache le menu d'accueil
        //     document.getElementById('home-menu').style.display = 'none';
            
        //     // Affiche le menu des niveaux
        //     document.getElementById('level-menu').style.display = 'grid';
        // });

        // Ajoutons également un gestionnaire pour le bouton "Annuler" du menu des niveaux
        // afin de pouvoir revenir au menu d'accueil
        // document.getElementById('close-level-menu').addEventListener('click', function() {
        //     document.getElementById('level-menu').style.display = 'none';
        //     document.getElementById('home-menu').style.display = 'grid';
        // });

    class DropdownMenu {
    static allMenus = []; // Liste de tous les menus pour la fermeture automatique
    
    constructor(config) {
        this.buttonId = config.buttonId;
        this.menuId = config.menuId;
        this.display = config.display || 'block';
        this.closeOthers = config.closeOthers !== false;
        this.afterClose = config.afterClose || null;
        this.afterOpen = config.afterOpen || null;
        
        this.button = document.getElementById(this.buttonId);
        this.menu = document.getElementById(this.menuId);
        
        // Enregistrer dans la liste des menus
        DropdownMenu.allMenus.push(this);
        
        this.init();
    }
    
    init() {
        if (!this.button || !this.menu) return;
        
        // Gestionnaire pour le bouton
        this.button.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.toggle();
        });
    }
    
    toggle() {
        if (this.buttonId === 'hint-btn' && !isGameActive()) {
        return; // Empêcher l'ouverture
    }
        const isOpen = this.isOpen();
        
        if (isOpen) {
            this.close();
        } else {
            if (this.closeOthers) {
                DropdownMenu.closeAll(this.menuId);
            }
            this.open();
        }
    }
    
    open() {
        this.menu.style.display = this.display;
        if (this.afterOpen) this.afterOpen();
    }
    
    close() {
        this.menu.style.display = 'none';
        if (this.afterClose) this.afterClose();
    }
    
    isOpen() {
        return this.menu.style.display === this.display;
    }
    
    // Méthode statique pour fermer tous les menus sauf celui spécifié
    static closeAll(exceptMenuId = null) {
        DropdownMenu.allMenus.forEach(menu => {
            if (menu.menuId !== exceptMenuId) {
                menu.close();
            }
        });
    }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const backgroundMusic = document.getElementById('background-music');
        
        // Récupérer l'état de lecture précédent
        const wasPlaying = sessionStorage.getItem('musicWasPlaying') === 'true';
        const currentTime = parseFloat(sessionStorage.getItem('musicCurrentTime') || '0');
        
        // Appliquer le volume sauvegardé
        const savedVolume = localStorage.getItem('gameVolume') || 0.5;
        backgroundMusic.volume = savedVolume;
        
        // Si la musique jouait avant l'actualisation, la reprendre
        if (wasPlaying) {
            backgroundMusic.currentTime = currentTime;
            backgroundMusic.play().catch(e => console.error("Erreur de lecture audio:", e));
        }
        
        // Sauvegarder l'état de lecture périodiquement
        function saveMusicState() {
            if (backgroundMusic && !backgroundMusic.paused) {
                sessionStorage.setItem('musicWasPlaying', 'true');
                sessionStorage.setItem('musicCurrentTime', backgroundMusic.currentTime.toString());
            } else {
                sessionStorage.setItem('musicWasPlaying', 'false');
            }
        }
        
        // Sauvegarder l'état toutes les 2 secondes
        setInterval(saveMusicState, 2000);
        
        // Sauvegarder l'état avant que la page ne se ferme
        window.addEventListener('beforeunload', saveMusicState);
        
        // Gestionnaire pour le premier clic (nécessaire pour certains navigateurs)
        document.addEventListener('click', function initialPlay() {
            if (backgroundMusic.paused && wasPlaying) {
                backgroundMusic.play().catch(e => console.error("Erreur de lecture audio:", e));
            }
            document.removeEventListener('click', initialPlay);
        }, { once: true });
    });

    // Initialisation de tous les menus au chargement du document
    document.addEventListener('DOMContentLoaded', function() {


        document.addEventListener('touchmove', function(e) {
    // Si le touch est dans un dropdown, empêcher le scroll de la page
    if (e.target.closest('#sound-dropdown') || 
        e.target.closest('#hint-dropdown') ||
        e.target.closest('#leaderboard')) {
        e.preventDefault();
    }
}, { passive: false });

        const urlParams = new URLSearchParams(window.location.search);
    
    // Afficher le menu d'accueil si on arrive sans paramètres spécifiques
    // ou si on n'est pas déjà en jeu (par exemple après un rechargement)
    if (!urlParams.has('level') || (!savedState && !sessionStorage.getItem("inGame"))) {
        // console.log('Affichage du menu d\'accueil au chargement initial');
        MenuManager.showMenu('home-menu');
        sessionStorage.setItem("inGame", "true");    }

    document.getElementById('close-popup').addEventListener('click', closeWinPopup);

    document.getElementById('close-popup').addEventListener('click', closeWinPopup);
    // Définir homeLogoLink correctement en utilisant l'ID
    const homeLogoLink = document.getElementById('home-logo');
    
    // Gestionnaire unifié pour le bouton Home
    if (homeLogoLink) {
        homeLogoLink.addEventListener('click', function(e) {
            e.preventDefault();
            // console.log('Menu Home activé via MenuManager');
            
            // Modifier l'URL pour supprimer le paramètre "level" sans rechargement
            const url = new URL(window.location.href);
            url.searchParams.delete('level');
            history.pushState({}, '', url);
            
            MenuManager.showMenu('home-menu');
        });
    }

    let selectedDifficulty = 0; // Difficulté par défaut
    
    // Gestion du clic sur les options de difficulté
    function setupDifficultyOptions() {
        document.querySelectorAll('.difficulty-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                // Mettre à jour selectedDifficulty avec la valeur du bouton radio sélectionné
                selectedDifficulty = parseInt(this.value);
                // console.log(`Difficulté sélectionnée: ${selectedDifficulty}`);
                
                // Mettre à jour les classes visuelles
                document.querySelectorAll('.difficulty-option').forEach(option => {
                    option.classList.remove('selected');
                });

                sessionStorage.setItem('selectedDifficulty', selectedDifficulty.toString());

                this.closest('.difficulty-option').classList.add('selected');
            });
        });
    }

// Initialiser les fonctionnalités lorsque le menu est affiché
document.getElementById('random-generator').addEventListener('click', function() {
    MenuManager.showMenu('random-level-menu');
    // Initialiser les options de difficulté après l'affichage du menu
    setupDifficultyOptions();
});

    // Gestionnaire pour les boutons de niveau
    document.querySelectorAll('.level-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Si le bouton n'est pas une flèche de navigation
            if (!this.querySelector('ion-icon')) {
                MenuManager.closeMenus();
                // Si vous voulez naviguer vers un niveau
                window.location.href = `play.php?level=${this.textContent.trim()}`;
            }
        });
    });
    document.getElementById('my-levels').addEventListener('click', function() {
        MenuManager.showMenu('my-levels-menu');
        loadMyLevels();
    });
    
    // Gestionnaire pour le bouton Campaign
    document.getElementById('campaign').addEventListener('click', function() {
        MenuManager.showMenu('level-menu');
    });

    updateButtonState();

    // Flèche droite du menu 1 vers menu 2
    document.getElementById('arrow-right-1').addEventListener('click', function() {
        MenuManager.showMenu('level-menu-2');
    });

    // Flèche gauche du menu 2 vers menu 1
    document.getElementById('arrow-left-2').addEventListener('click', function() {
        MenuManager.showMenu('level-menu');
    });

    // Flèche droite du menu 2 vers menu 3
    document.getElementById('arrow-right-2').addEventListener('click', function() {
        MenuManager.showMenu('level-menu-3');
    });

    // Flèche gauche du menu 3 vers menu 2
    document.getElementById('arrow-left-3').addEventListener('click', function() {
        MenuManager.showMenu('level-menu-2');
    });
    
    // Pour le bouton "Editor"
    document.getElementById('editor').addEventListener('click', function() {
        MenuManager.showMenu('editor-choice-menu');
    });

    document.getElementById('manual-editor').addEventListener('click', function() {
    MenuManager.showMenu('editor-menu');
    });

    
    document.getElementById('back-to-editor-choice-menu').addEventListener('click', function() {
    MenuManager.showMenu('editor-choice-menu');
});

    // Pour le bouton "Random Level"
    document.getElementById('random-generator').addEventListener('click', function() {
        MenuManager.showMenu('random-level-menu');
    });

    // Pour le bouton "Custom Levels"
    document.getElementById('custom-levels').addEventListener('click', function() {
        MenuManager.showMenu('custom-levels-menu');
    });

    // Pour le bouton "Skins"
    document.getElementById('skins').addEventListener('click', function() {
        MenuManager.showMenu('skins-menu');
        highlightSelectedSkins();
    });
    
    // Afficher automatiquement le menu d'accueil au chargement initial
    if (!savedState && !sessionStorage.getItem("inGame")) {
        MenuManager.showMenu('home-menu');
        sessionStorage.setItem("inGame", "true");
    }

    // Pour le bouton "My Levels"
    document.getElementById('my-levels').addEventListener('click', function() {
        MenuManager.showMenu('my-levels-menu');
    });

    // Pour le bouton "Custom Levels" dans le menu My Levels (retour)
    document.getElementById('back-to-custom-levels').addEventListener('click', function() {
        MenuManager.showMenu('custom-levels-menu');
    });


    // 4. Gestion des boutons de niveau
    document.querySelectorAll('.level-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (levelMenu) levelMenu.style.display = 'none';
            if (grid) grid.style.display = '';
        });
    });

        // Fermer les menus en cliquant ailleurs
        document.addEventListener('click', (e) => {
            // Ne pas fermer les menus si on clique sur un bouton qui ouvre un menu
            if (e.target.closest('#sound-btn') || 
                e.target.closest('#hint-btn') || 
                e.target.closest('#home-logo')) {
                return;
            }
            
                if (e.target.closest('#sound-dropdown') || 
                e.target.closest('#hint-dropdown') || 
                e.target.closest('#leaderboard') ||    // Ajout du menu leaderboard
                e.target.closest('#level-menu') ||
                e.target.closest('#home-menu')) {
                return;
            }
            
            // Si on clique n'importe où ailleurs, fermer tous les menus
            DropdownMenu.closeAll();
        });
        
        // Menu indice
        const hintMenu = new DropdownMenu({
            buttonId: 'hint-btn',
            menuId: 'hint-dropdown'
        });

        // Menu leaderboard


new DropdownMenu({
    buttonId: 'menu-icon-btn',
    menuId: 'leaderboard',
    display: 'block',
    afterOpen: () => {
        // Ajouter des vérifications de nullité pour éviter les erreurs
        const menuIconBtn = document.getElementById('menu-icon-btn');
        const leaderboardElement = document.getElementById('leaderboard');
        
        // Ajouter .open AU BOUTON ET au menu seulement s'ils existent
        if (menuIconBtn) menuIconBtn.classList.add('open');
        if (leaderboardElement) {
            leaderboardElement.classList.add('open');
            // Forcer le repaint
            void leaderboardElement.offsetWidth;
        }
    },
    afterClose: () => {
        // Ajouter des vérifications de nullité pour éviter les erreurs
        const menuIconBtn = document.getElementById('menu-icon-btn');
        const leaderboardElement = document.getElementById('leaderboard');
        
        // Retirer .open DES DEUX éléments seulement s'ils existent
        if (menuIconBtn) menuIconBtn.classList.remove('open');
        if (leaderboardElement) leaderboardElement.classList.remove('open');
    }
});

const leaderboardMenu = DropdownMenu.allMenus.find(m => m.menuId === 'leaderboard');
if (leaderboardMenu) {
    leaderboardMenu.close = function() {
        // Appeler afterClose seulement s'il existe
        if (this.afterClose) this.afterClose();
    };
    
    leaderboardMenu.isOpen = function() {
        const leaderboardElement = document.getElementById('leaderboard');
        // Vérifier si l'élément existe avant d'accéder à ses propriétés
        return leaderboardElement && leaderboardElement.classList.contains('open');
    };
}     
 /*/ Menu niveaux
        const levelMenu = new DropdownMenu({
            buttonId: 'home-logo',
            menuId: 'level-menu',
            display: 'flex',
            afterOpen: () => {
                document.querySelector('.game-wrapper').style.display = 'none';
            },
            afterClose: () => {
                document.querySelector('.game-wrapper').style.display = '';
            }
        });*/
        
        // Bouton pour fermer le menu des niveaux
        // document.getElementById('close-level-menu').addEventListener('click', () => {
        //     const levelMenu = DropdownMenu.allMenus.find(m => m.menuId === 'level-menu');
        //     if (levelMenu) levelMenu.close();
        // });

        // Fix pour les boutons de niveau
        document.querySelectorAll('.level-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                window.location.href = `play.php?level=${this.textContent.trim()}`;
            });
        });

        // Configuration du volume et gestion audio
        const volumeSlider = document.getElementById('volume-slider');
        const backgroundMusic = document.getElementById('background-music');
        
        if (volumeSlider && backgroundMusic) {
                let isSliderActive = false;

                volumeSlider.addEventListener('touchstart', function(e) {
                    isSliderActive = true;
                    e.stopPropagation(); // Empêche la propagation vers la grille
                }, { passive: true });

                volumeSlider.addEventListener('touchmove', function(e) {
                    if (isSliderActive) {
                        e.stopPropagation(); // Empêche la propagation vers la grille
                        // NE PAS appeler preventDefault() ici pour permettre le fonctionnement du slider
                    }
                }, { passive: true });

                volumeSlider.addEventListener('touchend', function(e) {
                    isSliderActive = false;
                    e.stopPropagation(); // Empêche la propagation vers la grille
                }, { passive: true });

            // Charger le volume sauvegardé
            const savedVolume = localStorage.getItem('gameVolume') || 0.5;
            backgroundMusic.volume = savedVolume;
            volumeSlider.value = savedVolume * 100;
            volumeSlider.style.setProperty('--volume-percent', `${Math.round(savedVolume * 100)}%`);
            
            // Mettre à jour l'icône au chargement
            const soundIcon = document.querySelector('#sound-btn img');
            if (soundIcon) {
                if (savedVolume == 0) {
                    soundIcon.src = "icons/muted.png";
                    soundIcon.alt = "Muted";
                } else {
                    soundIcon.src = "icons/sound.png";
                    soundIcon.alt = "Sound";
                }
            }
                // Mettre à jour le volume en direct
                volumeSlider.addEventListener('input', function() {
                    const value = this.value;
                    const audioVolume = value / 100;
                    
                    this.style.setProperty('--volume-percent', `${value}%`);
                    
                    // Mettre à jour le volume de tous les sons
                    const audios = document.querySelectorAll('audio');
                    audios.forEach(audio => {
                        audio.volume = audioVolume;
                    });
                    

            // MODIFICATION : Gestion tactile plus précise pour le slider
 
                
                // Mettre à jour l'icône
                const soundIcon = document.querySelector('#sound-btn img');
                if (soundIcon) {
                    if (audioVolume === 0) {
                        soundIcon.src = "icons/muted.png";
                        soundIcon.alt = "Muted";
                    } else {
                        soundIcon.src = "icons/sound.png";
                        soundIcon.alt = "Sound";
                    }
                }
                
                // Sauvegarder le réglage
                localStorage.setItem('gameVolume', audioVolume);
            });
        }
            const soundDropdown = document.getElementById('sound-dropdown');
    if (soundDropdown) {
        soundDropdown.addEventListener('touchmove', function(e) {
            // Permettre le scroll normal dans le dropdown, mais empêcher la propagation vers la page
            e.stopPropagation();
        }, { passive: true });
    }

        // Fix pour le hint
        document.getElementById('hint-only-btn').addEventListener('click', function() {
            const hintBtn = document.getElementById('hint-btn');
            if (hintBtn) hintBtn.classList.add('loading');
            prepareHintForm('hint');
            
            // Utiliser fetch au lieu de soumettre le formulaire
            const formData = new FormData(document.getElementById('hint-form'));
            
            fetch('game/scripts/solveur.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (hintBtn) hintBtn.classList.remove('loading');
                if (data.solution) {
                    // Si une solution existe (même "No path found")
                    sessionStorage.setItem("solution", data.solution);
                } else if (data.error) {
                    // Si une erreur est retournée, définir comme "No path found"
                    // console.error("Erreur du solveur:", data.error);
                    sessionStorage.setItem("solution", "No path found.");
                } else {
                    // Cas par défaut si la structure de réponse est inattendue
                    // console.warn("Format de réponse inattendu:", data);
                    sessionStorage.setItem("solution", "No path found.");
                }
                showVisualHint();
            })
            .catch(error => {
                if (hintBtn) hintBtn.classList.remove('loading');
                // console.error("Erreur lors de la récupération de l'indice:", error);
                
                // En cas d'erreur, définir aussi "No path found" et afficher les croix
                sessionStorage.setItem("solution", "No path found.");
                showVisualHint(); // Affichera les croix rouges
            });
            
            // Fermer le dropdown
            DropdownMenu.closeAll();
        });


        // document.getElementById('run-solution').addEventListener('click', function() {
        //     prepareHintForm('solution');
        //     document.getElementById('hint-form').submit();
        // });
    });

    function getRandomWallTexture() {
    // Générer un nombre aléatoire entre 1 et 9
    const textureNumber = Math.floor(Math.random() * 5) + 1;
    return `wall-${textureNumber}`;
}

        const EMPTY = -1;
        const VISITED = 0;
        const PATH = 1;
        const WALL = 2;
        const TP = 3;
        const HOLE = 4;
        const PLAYER = 5;

        let rows, cols, playerPos, playground;
        let originalPlayerPos, originalPlayground;
        let hintTimeout = null;
        let highlightedCells = [];
        let isSolverRunning = false;
        let solveUsed = false;
        let hintUsed = false;
        let wallTextures = [];
        let pathTextures = [];
        let previousDirection = null;

        <?php
            $savedState = null;
            if (isset($_SESSION['current_state'])) {
                $savedState = json_decode($_SESSION['current_state'], true);
                unset($_SESSION['current_state']); // Nettoyer après utilisation
            }
            $savedplayerPos=null;
            if (isset($_SESSION['savedplayerPos'])) {
                $savedplayerPos = json_decode($_SESSION['savedplayerPos'], true);
                unset($_SESSION['savedplayerPos']); // Nettoyer après utilisation
            }
            $mode=null;
            if (isset($_SESSION['mode'])) {
                $mode = json_decode($_SESSION['mode'], true);
                unset($_SESSION['mode']); // Nettoyer après utilisation
            }
        ?>

        const savedState = <?= $savedState ? json_encode($savedState) : 'null' ?>;
        const savedplayerPos = <?= $savedplayerPos ? json_encode($savedplayerPos) : 'null' ?>;
        const mode = <?= $mode ? json_encode($mode) : 'null' ?>;
        
        async function loadLevel(level) {
            if (!level || level <= 0) return false;
            const levelData = {id: level};

            try {
                const response = await fetch("game/scripts/load.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(levelData)
                });

                const result = await response.json();
                if (!result.success) throw new Error(result.message || "Error while loading level");
                sessionStorage.setItem("levelName", result.levelName || `Level ${level}`);
                sessionStorage.setItem("solution", result.solution);
                sessionStorage.setItem("minMoves", result.minMoves.toString());
                sessionStorage.setItem("user_id", result.user_id.toString());
                
                const isCampaign = result.is_campaign === 1 || result.is_campaign === true;
                sessionStorage.setItem("isCampaign", result.is_campaign ? "true" : "false");
                const headerTitle = document.querySelector('.controls .title');
                if (headerTitle) {
                    headerTitle.textContent = result.levelName || `Level ${level}`;
                }
                return decodeJSON(result.data);
            } catch (error) {
                // console.error("Error:", error);
                alert("Error while loading level: " + error.message);
            }
        }

        function decodeJSON(data) {
            const rows = data[0];
            const cols = data[1];
            const playground = data.slice(2);

            const playerIndex = playground.indexOf(PLAYER);
            const playerPos = {row: Math.floor(playerIndex / cols), col: playerIndex % cols};
            
            const playgroundGrid = [];
            for (let i = 0; i < playground.length; i += cols) {
                playgroundGrid.push(playground.slice(i, i + cols));
            }

            return {
                rows: rows,
                cols: cols,
                playerPos: playerPos,
                playground: playgroundGrid
            };
        }

function showWinPopup(moves) {
    // Commencer par faire disparaître la grille avec une transition
    const grid = document.getElementById('grid');
    if (grid) {
        grid.classList.add('fade-out');
    }
    
    // Récupérer le niveau actuel
    const level = parseInt(new URLSearchParams(window.location.search).get('level') || "1");
    const isCampaignString = sessionStorage.getItem("isCampaign");
    const isCampaignLevel = isCampaignString === "true";
    const isLastCampaignLevel = isCampaignLevel && level === 30;

    // console.log("Debug level:", {
    //     level: level,
    //     isCampaignString: isCampaignString,
    //     isCampaignLevel: isCampaignLevel,
    //     isLastCampaignLevel: isLastCampaignLevel
    // });
    // Message par défaut
    let winMessage = `You completed the level in ${moves} moves!`;


    // Fonction unique pour afficher le popup avec le message approprié
    function finallyDisplayPopup(message) {
        // Attendre que la grille s'estompe COMPLÈTEMENT avant d'afficher le popup
        setTimeout(() => {
            // Mettre à jour le message de victoire
            document.getElementById('win-message').innerHTML = message;
            
            // Cacher complètement la grille et tous les autres menus
            // IMPORTANT: Le display: none ne doit arriver qu'après la fin de la transition
            grid.style.display = 'none';
            MenuManager.hideAllMenus();
            updateWinPopupButtons(isCampaignLevel, isLastCampaignLevel);
            
            // Préparer le popup
            const popup = document.getElementById('win-popup');
            popup.style.display = 'grid';
            
            // Utiliser une double requestAnimationFrame pour s'assurer que le DOM 
            // a bien été actualisé avant de déclencher la transition
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    // Déclencher la transition d'opacité
                    popup.classList.add('show');
                    updateButtonState();
                    gameEnded = true;
                });
            });
        }, 300);
    }
    
    // Récupérer le numéro du niveau actuel
    const currentLevel = parseInt(new URLSearchParams(window.location.search).get('level') || "1");
    
    // Débloquer le niveau suivant
    LevelManager.unlockLevel(currentLevel + 1);
    
    // Si l'utilisateur est connecté, envoyer le score
    if (window.isLoggedIn && !solverUsed && !hintUsed) {
        fetch('game/scripts/scores.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `level=${level}&moves=${moves}`
        })
        .then(response => response.json())
        .then(data => {
            // console.log('Résultat sauvegarde:', data);
            
            // Si c'est un nouveau record, modifier le message
            if (data.success && data.newRecord) {
                winMessage = `You completed the level in ${moves} moves!<br>New best score!`;
                if (solverUsed || hintUsed) {
                    winMessage += `<p class="record-not-saved">record not saved (${solverUsed ? 'solver' : 'hint'} used)</p>`;
                }

                
            }
            
            // Afficher le popup une seule fois, avec le bon message
            finallyDisplayPopup(winMessage);
        })
        .catch(error => {
            console.error('Erreur:', error);
            // En cas d'erreur, afficher le message de base
            finallyDisplayPopup(winMessage);
        });
    } else {
        if (!window.isLoggedIn) {
            // Utilisateur non connecté
            winMessage += `<br><span style='color:#FF6D1B;'>Record not saved (user not logged in)</span>`;
        } else if (solverUsed || hintUsed) {
            // Utilisateur connecté mais a utilisé solver/hint
            winMessage += `<br><span style='color:#FF6D1B;'>Record not saved (${solverUsed ? 'solver' : 'hint'} used)</span>`;
        }
        finallyDisplayPopup(winMessage);
    }
}



function closeWinPopup() {
    const popup = document.getElementById('win-popup');
    
    // Déclencher la transition inverse
    popup.classList.remove('show');
    
    // Attendre la fin de la transition avant de cacher l'élément
    setTimeout(() => {
        popup.style.display = 'none';
        
        // Réafficher la grille et retirer la classe fade-out
        const grid = document.getElementById('grid');
        if (grid) {
            grid.style.display = '';
            grid.classList.remove('fade-out'); // Important : retirer la classe
        }
        
        updateButtonState();
    }, 500); // Correspond à la durée de la transition CSS
}

function isGameActive() {
    const grid = document.getElementById('grid');
    const levelMenu = document.getElementById('level-menu');
    const homeMenu = document.getElementById('home-menu');
    const winPopup = document.getElementById('win-popup');
    
    // Le jeu est actif si la grille est visible ET aucun menu n'est ouvert
    return grid && grid.style.display !== 'none' && 
           (!levelMenu || levelMenu.style.display === 'none') &&
           (!homeMenu || homeMenu.style.display === 'none') &&
           (!winPopup || winPopup.style.display === 'none');
}

function updateButtonState() {
    const hintBtn = document.getElementById('hint-btn');
    const restartBtn = document.getElementById('new-game');
    const homeBtn = document.getElementById('home-logo');

    // Pour les boutons hint et restart
    if (isGameActive() && !isSolverRunning) {
        if (hintBtn) {
            hintBtn.classList.remove('btn-disabled');
            hintBtn.style.pointerEvents = '';
        }
        if (restartBtn) {
            restartBtn.classList.remove('btn-disabled');
            restartBtn.style.pointerEvents = '';
        }
    } else {
        if (hintBtn) {
            hintBtn.classList.add('btn-disabled');
            hintBtn.style.pointerEvents = 'none';
        }
        if (restartBtn) {
            restartBtn.classList.add('btn-disabled');
            restartBtn.style.pointerEvents = 'none';
        }
    }
    
    // Pour le bouton home: UNIQUEMENT désactivé quand le solveur est en cours
    if (homeBtn) {
        if (isSolverRunning) {
            homeBtn.classList.add('btn-disabled');
            homeBtn.style.pointerEvents = 'none';
        } else {
            homeBtn.classList.remove('btn-disabled');
            homeBtn.style.pointerEvents = '';
        }
    }
}
        
function isAnyDropdownOpen() {
    return DropdownMenu.allMenus.some(menu => menu.isOpen());
}

    function goToNextLevel() {
        const params = new URLSearchParams(window.location.search);
        let currentLevel = parseInt(params.get('level'), 10);
        if (isNaN(currentLevel)) currentLevel = 1;
        let nextLevel = currentLevel + 1;
        window.location.href = `play.php?level=${nextLevel}`;
        }           

        function showTip() {
            const solution = sessionStorage.getItem("solution");
            if (!solution || solution.length === 0 || solution === "No path found.") return;

            // Annuler le clignotement précédent s'il existe
            clearHint();

            // Trouver la première direction de la solution
            const firstMove = solution[0];
            highlightedCells = [];

            // Déterminer les cellules à mettre en surbrillance selon le premier mouvement
            switch (firstMove) {
                case 'N': // Nord - toute la colonne vers le haut jusqu'au premier mur
                    for (let row = playerPos.row - 1; row >= 0; row--) {
                        if (playground[row][playerPos.col] === WALL || playground[row][playerPos.col] === HOLE) break;
                        highlightedCells.push({row: row, col: playerPos.col});
                    }
                    break;
                case 'S': // Sud - toute la colonne vers le bas jusqu'au premier mur
                    for (let row = playerPos.row + 1; row < rows; row++) {
                        if (playground[row][playerPos.col] === WALL || playground[row][playerPos.col] === HOLE) break;
                        highlightedCells.push({row: row, col: playerPos.col});
                    }
                    break;
                case 'E': // Est - toute la ligne vers la droite jusqu'au premier mur
                    for (let col = playerPos.col + 1; col < cols; col++) {
                        if (playground[playerPos.row][col] === WALL || playground[playerPos.row][col] === HOLE) break;
                        highlightedCells.push({row: playerPos.row, col: col});
                    }
                    break;
                case 'O': // Ouest - toute la ligne vers la gauche jusqu'au premier mur
                    for (let col = playerPos.col - 1; col >= 0; col--) {
                        if (playground[playerPos.row][col] === WALL || playground[playerPos.row][col] === HOLE) break;
                        highlightedCells.push({row: playerPos.row, col: col});
                    }
                    break;
            }

            // Filtrer seulement les cellules valides
            highlightedCells = highlightedCells.filter(pos => 
                pos.row >= 0 && pos.row < rows && 
                pos.col >= 0 && pos.col < cols
            );

            // Ajouter la classe de clignotement
            highlightedCells.forEach(pos => {
                const cell = document.querySelector(`.cell[data-row="${pos.row}"][data-col="${pos.col}"]`);
                if (cell) {
                    cell.classList.add('hint');
                }
            });
            
            // Configurer le timeout pour retirer le clignotement
            hintTimeout = setTimeout(clearHint, 3000);
        }

        // Fonction pour effacer le clignotement
        function clearHint() {
            if (hintTimeout) {
                clearTimeout(hintTimeout);
                hintTimeout = null;
            }
            
            highlightedCells.forEach(pos => {
                const cell = document.querySelector(`.cell[data-row="${pos.row}"][data-col="${pos.col}"]`);
                if (cell) {
                    cell.classList.remove('hint');
                }
            });
            
            highlightedCells = [];
        }

let inputBlocked = false;

function markNoSolutionNeighbors() {
    // Effacer d'abord toutes les marques précédentes
    clearNoSolutionMarkers();
    
    // console.log("DEBUG: Marking no-solution neighbors around position:", playerPos);
    
    inputBlocked = true;
    isSolverRunning = false;
    updateButtonState();
    // Directions possibles: haut, droite, bas, gauche
    const directions = [
        { row: -1, col: 0 }, // haut
        { row: 0, col: 1 },  // droite
        { row: 1, col: 0 },  // bas
        { row: 0, col: -1 }  // gauche
    ];
    
    // Vérifier et marquer chaque cellule voisine
    directions.forEach(dir => {
        const newRow = playerPos.row + dir.row;
        const newCol = playerPos.col + dir.col;
        
        // Vérifier si la cellule est dans les limites du plateau
        if (newRow >= 0 && newRow < rows && newCol >= 0 && newCol < cols) {
            // Check if it's a path cell (PATH or VISITED)
            if (playground[newRow][newCol] === PATH || playground[newRow][newCol] === VISITED) {
                // console.log("DEBUG: Adding no-solution marker at:", newRow, newCol);
                const cell = document.querySelector(`.cell[data-row="${newRow}"][data-col="${newCol}"]`);
                if (cell) {
                    // Créer un marqueur visible au lieu d'utiliser pseudo-éléments
                    const marker = document.createElement('div');
                    marker.className = 'no-solution-marker-element';
                    marker.style.cssText = `
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        z-index: 25;
                        pointer-events: none;
                    `;
                    
                    // Créer les croix directement dans le DOM
                    const line1 = document.createElement('div');
                    line1.style.cssText = `
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        width: 70%;
                        height: 4px;
                        background-color: red;
                        transform: translate(-50%, -50%) rotate(45deg);
                        animation: flash-error 1s infinite;
                    `;
                    
                    const line2 = document.createElement('div');
                    line2.style.cssText = `
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        width: 70%;
                        height: 4px;
                        background-color: red;
                        transform: translate(-50%, -50%) rotate(-45deg);
                        animation: flash-error 1s infinite;
                    `;
                    
                    marker.appendChild(line1);
                    marker.appendChild(line2);
                    cell.appendChild(marker);
                    
                    // Ajouter aussi la classe pour compatibilité
                    cell.classList.add('no-solution-marker');
                } // else {
                    // console.error("Cell not found for coordinates:", newRow, newCol);
                // }
            }
        }
    });
    
    // Définir un délai pour effacer les marqueurs après 3 secondes
}

function clearNoSolutionMarkers() {
    // console.log("DEBUG: Clearing markers");
    
    // Supprimer les éléments marqueurs
    document.querySelectorAll('.no-solution-marker-element').forEach(marker => {
        if (marker.parentNode) {
            marker.parentNode.removeChild(marker);
        }
    });
    
    // Supprimer aussi les classes
    document.querySelectorAll('.no-solution-marker').forEach(cell => {
        cell.classList.remove('no-solution-marker');
    });
}


function goToCustomLevels() {
    const popup = document.getElementById('win-popup');
    
    // Déclencher la transition inverse pour masquer le popup
    popup.classList.remove('show');
    
    // Attendre la fin de la transition avant de cacher l'élément
    setTimeout(() => {
        popup.style.display = 'none';
        
        // Retirer la classe fade-out si elle existe encore
        const grid = document.getElementById('grid');
        if (grid) {
            grid.classList.remove('fade-out');
        }
        
        // Afficher le menu des niveaux personnalisés
        MenuManager.showMenu('custom-levels-menu');
        loadCustomLevels(); // Recharger la liste des niveaux
    }, 400);
}

function updateWinPopupButtons(isCampaignLevel, isLastCampaignLevel) {
    const popupContent = document.querySelector('.popup-content');
    
    // Supprimer les boutons existants
    const existingButtons = popupContent.querySelectorAll('button');
    existingButtons.forEach(btn => btn.remove());
    
    if (isCampaignLevel) {
        // Niveau de campagne
        
        // AJOUT : Bouton "Retry" pour les niveaux de campagne
        const retryBtn = document.createElement('button');
        retryBtn.textContent = 'Retry';
        retryBtn.onclick = retryLevel;
        popupContent.appendChild(retryBtn);
        
        if (!isLastCampaignLevel) {
            // Pas le dernier niveau : afficher "Next Level"
            const nextLevelBtn = document.createElement('button');
            nextLevelBtn.textContent = 'Next Level';
            nextLevelBtn.onclick = goToNextLevel;
            popupContent.appendChild(nextLevelBtn);
        }
        
        // Bouton "Campaign"
        const campaignBtn = document.createElement('button');
        campaignBtn.textContent = 'Campaign';
        campaignBtn.onclick = goToCampaign;
        popupContent.appendChild(campaignBtn);
        
    } else {
        // Niveau personnalisé : "Retry" et "Custom Levels"
        const retryBtn = document.createElement('button');
        retryBtn.textContent = 'Retry';
        retryBtn.onclick = retryLevel;
        popupContent.appendChild(retryBtn);
        
        const customLevelsBtn = document.createElement('button');
        customLevelsBtn.textContent = 'Custom Levels';
        customLevelsBtn.onclick = goToCustomLevels;
        popupContent.appendChild(customLevelsBtn);
    }
}

function retryLevel() {
    // Simplement recharger la page
    window.location.reload();
}

function goToCampaign() {
    const popup = document.getElementById('win-popup');
    
    // Déclencher la transition inverse pour masquer le popup
    popup.classList.remove('show');
    
    // Attendre la fin de la transition avant de cacher l'élément
    setTimeout(() => {
        popup.style.display = 'none';
        
        // Retirer la classe fade-out si elle existe encore
        const grid = document.getElementById('grid');
        if (grid) {
            grid.classList.remove('fade-out');
        }
        
        // Afficher le menu de campagne
        MenuManager.showMenu('level-menu');
    }, 400);
}

        document.addEventListener('DOMContentLoaded', () => {
            const grid = document.getElementById('grid');
            const newGameButton = document.getElementById('new-game');         
            const urlParams = new URLSearchParams(window.location.search);
            const levelName = urlParams.get('level');   
            
            let isMoving = false;
            let animationFrameId = null;
            let TPs = [];
            let gameEnded = false; // Ajout pour détecter la fin du jeu

            if (savedState) {
                // Décoder l'état sauvegardé comme un niveau normal
                const savedLevelData = decodeJSON(savedState);
                if (savedplayerPos){
                    savedLevelData.playerPos= {row:savedplayerPos[0], col:savedplayerPos[1]};
                }

                // Mettre à jour les références originales
                rows = savedLevelData.rows;
                cols = savedLevelData.cols;

                originalPlayerPos = {...savedLevelData.playerPos}; 
                originalPlayground = JSON.parse(JSON.stringify(savedLevelData.playground));
                
                // Initialiser avec l'état sauvegardé
                playerPos = {...originalPlayerPos};
                playground = JSON.parse(JSON.stringify(originalPlayground));
                
                initGame();
            }
            else{
                loadLevel(levelName).then(levelData => {
                    if (levelData) {
                        rows = levelData.rows;
                        cols = levelData.cols;
                        
                        // Sauvegarde de l'état original
                        originalPlayerPos = {...levelData.playerPos};
                        originalPlayground = JSON.parse(JSON.stringify(levelData.playground));
                        
                        // Initialisation avec les valeurs originales
                        playerPos = {...originalPlayerPos};
                        playground = JSON.parse(JSON.stringify(originalPlayground));
                        
                        initGame();
                    }
                }).catch(error => {
                    console.error("Error while loading:", error);
                });
            }


            

            const soundBtn = document.getElementById('sound-btn');
            const soundIcon = soundBtn.querySelector('img');
            const soundDropdown = document.getElementById('sound-dropdown');
            if (soundDropdown) {
                soundDropdown.addEventListener('touchmove', function(e) {
                    // Permettre le scroll normal dans le dropdown, mais empêcher la propagation vers la page
                    e.stopPropagation();
                }, { passive: true });
            }
            const volumeSlider = document.getElementById('volume-slider');
            let audioVolume = 0.5; // Valeur par défaut

            const backgroundMusic = document.getElementById('background-music');

            const savedVolume = localStorage.getItem('gameVolume');
            if (savedVolume !== null) {
                audioVolume = parseFloat(savedVolume);
            }

            // Lancer le son après la première interaction utilisateur
            document.addEventListener('click', function initialPlay() {
                backgroundMusic.play().catch(e => console.error("Erreur de lecture audio:", e));
                document.removeEventListener('click', initialPlay); // Ne s'exécute qu'une fois
            }, { once: true });

            // Appliquez le volume sauvegardé immédiatement
            if (backgroundMusic && savedVolume !== null) {
                backgroundMusic.volume = parseFloat(savedVolume);
            }

            function updateSoundIcon(volume) {
                if (volume === 0) {
                    soundIcon.src = "icons/muted.png";
                    soundIcon.alt = "Muted";
                } else {
                    soundIcon.src = "icons/sound.png";
                    soundIcon.alt = "Sound";
                }
            }

            // Initialisez le volume pour tous les éléments audio
            function updateAllAudioElements() {
                const audios = document.querySelectorAll('audio');
                audios.forEach(audio => {
                    audio.volume = audioVolume;
                    audio.muted = (audioVolume === 0);
                });
            }

            // Gestion du slider de volume
            if (volumeSlider) {
                volumeSlider.addEventListener('input', function() {
                    const value = this.value;
                    audioVolume = value / 100;
                    
                    this.style.setProperty('--volume-percent', `${value}%`);

                    updateSoundIcon(audioVolume);
                    // Mettre à jour le volume de tous les sons
                    updateAllAudioElements();
                    
                    // Sauvegarder le réglage dans localStorage
                    localStorage.setItem('gameVolume', audioVolume);
                });

                // Charger le volume sauvegardé
                const savedVolume = localStorage.getItem('gameVolume');
                if (savedVolume !== null) {
                    audioVolume = parseFloat(savedVolume);
                    volumeSlider.value = audioVolume * 100;
                    volumeSlider.style.setProperty('--volume-percent', `${Math.round(audioVolume * 100)}%`);
                    updateAllAudioElements();
                }
            }

            // Ajouter juste après l'initialisation du hint-dropdown
            new DropdownMenu({
                buttonId: 'sound-btn',
                menuId: 'sound-dropdown',
                display: 'flex', // Important: utiliser 'flex' au lieu de 'block'
                afterOpen: () => {
                    // Mettre à jour l'icône si nécessaire
                    const soundIcon = document.querySelector('#sound-btn img');
                    if (soundIcon && audioVolume === 0) {
                        soundIcon.src = "icons/muted.png";
                        soundIcon.alt = "Muted";
                    }
                    const volumeSlider = document.getElementById('volume-slider');
                    if (volumeSlider) {
                        volumeSlider.value = audioVolume * 100;
                        volumeSlider.style.setProperty('--volume-percent', `${Math.round(audioVolume * 100)}%`);
                    }
                }
            });


            // Fermer le dropdown du son en cliquant ailleurs
            document.addEventListener('click', (e) => {
                if (e.target.closest('#sound-btn') || 
                    e.target.closest('#hint-btn') || 
                    e.target.closest('#home-logo')) {
                    return;
                }
                
                // Ne pas fermer un menu si on clique à l'intérieur de celui-ci
                if (e.target.closest('#sound-dropdown') || 
                    e.target.closest('#hint-dropdown') || 
                    e.target.closest('#level-menu')) {
                    return;
                }
                
                // Si on clique n'importe où ailleurs, fermer tous les menus
                DropdownMenu.closeAll();
            });

            // Ajouter ceci près des autres gestionnaires d'événements
            document.getElementById('hint-form').addEventListener('submit', function(e) {
                e.preventDefault(); // Empêcher TOUTE soumission directe du formulaire
                // console.log("Soumission de formulaire empêchée");
                return false;
            });

            function initGame() {
                // Réinitialisation à l'état original
                // playerPos = {...originalPlayerPos};
                // playground = JSON.parse(JSON.stringify(originalPlayground));
                
                
                previousDirection = null;
                gameEnded = false; // Réinitialiser l'état du jeu
                isMoving = false;
                solverUsed = false;
                hintUsed = false;

                if (animationFrameId) {
                    cancelAnimationFrame(animationFrameId);
                }

                grid.innerHTML = '';
                TPs = [];

                grid.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
                grid.style.gridTemplateRows = `repeat(${rows}, 1fr)`;

                if (wallTextures.length === 0) {
                    // Créer une carte des textures de murs
                    for (let row = 0; row < rows; row++) {
                        wallTextures[row] = [];
                        for (let col = 0; col < cols; col++) {
                            if (playground[row][col] === WALL) {
                                wallTextures[row][col] = getRandomWallTexture();
                            }
                        }
                    }
                }

                if (pathTextures.length === 0) {
                    // Créer une carte des textures de sols seulement la première fois
                    for (let row = 0; row < rows; row++) {
                        pathTextures[row] = [];
                        for (let col = 0; col < cols; col++) {
                            if (originalPlayground[row][col] === PATH) { // Utiliser originalPlayground pour détecter les chemins
                                pathTextures[row][col] = getRandomPathTexture();
                            }
                        }
                    }
                }


                for (let row = 0; row < rows; row++) {
                    for (let col = 0; col < cols; col++) {
                        if (playground[row][col] === TP) {
                            TPs.push({ row, col });
                        }
                    }
                }

                isMoving = false;

                for (let row = 0; row < rows; row++) {
                    for (let col = 0; col < cols; col++) {
                        const cell = document.createElement('div');
                        cell.className = 'cell';
                        cell.dataset.row = row;
                        cell.dataset.col = col;

                        if (playground[row][col] === WALL) {
                            cell.classList.add('wall');
                            cell.classList.add(wallTextures[row][col] || getRandomWallTexture());
                        } else if (playground[row][col] === TP) {
                            cell.classList.add('tp');
                        } else if (playground[row][col] === HOLE) {
                            cell.classList.add('hole');
                        } else if (playground[row][col] === PATH || originalPlayground[row][col] === PATH) {
                            cell.classList.add('path');
                            if (pathTextures[row] && pathTextures[row][col]) {
                                cell.classList.add(pathTextures[row][col]);
                            }
                        }

                        const fill = document.createElement('div');
                        fill.className = 'fill';
                        cell.appendChild(fill);

                        if(playground[row][col]===PLAYER){
                            playground[row][col] = VISITED;
                        }

                        if (playground[row][col] === VISITED || (playground[row][col]===PATH && (col === playerPos.col && row === playerPos.row)) || playground[row][col]===PLAYER) {
                            cell.classList.add('visited');
                        }

                        if (row === playerPos.row && col === playerPos.col) {
                            const player = document.createElement('div');
                            player.className = 'player';
                            cell.appendChild(player);
                        }

                        grid.appendChild(cell);
                    }
                }

                // Appliquer le skin du joueur après l'initialisation du jeu
                applyPlayerSkin();
            }

            function getRandomPathTexture() {
                // Générer un nombre aléatoire entre 1 et le nombre de variantes de gravillons disponibles
                const textureNumber = Math.floor(Math.random() * 5) + 1; // Ajustez le nombre selon vos images
                return `gravillons-${textureNumber}`;
            }

            function animateMovement(startPos, endPos, direction, callback) {
                const playerElement = document.querySelector('.player');
                if (!playerElement) return;

                const startCell = grid.children[startPos.row * cols + startPos.col];
                const endCell = grid.children[endPos.row * cols + endPos.col];

                const gridRect = grid.getBoundingClientRect();
                const startCellRect = startCell.getBoundingClientRect();
                const endCellRect = endCell.getBoundingClientRect();

                const startX = startCellRect.left - gridRect.left + startCellRect.width / 2;
                const startY = startCellRect.top - gridRect.top + startCellRect.height / 2;
                const endX = endCellRect.left - gridRect.left + endCellRect.width / 2;
                const endY = endCellRect.top - gridRect.top + endCellRect.height / 2;

                const distanceX = endX - startX;
                const distanceY = endY - startY;

                playerElement.classList.add('animating');
                playerElement.style.position = 'fixed';
                playerElement.style.left = startCellRect.left + 'px';
                playerElement.style.top = startCellRect.top + 'px';
                playerElement.style.width = startCellRect.width + 'px';
                playerElement.style.height = startCellRect.height + 'px';
                playerElement.style.transform = 'translate(0, 0)';

                const startTime = performance.now();
                
                const cellsTraversed = Math.max(
                    Math.abs(endPos.row - startPos.row),
                    Math.abs(endPos.col - startPos.col)
                );

                const millisecondsPerCell = 30; // Changer la vitesse ici
                const duration = cellsTraversed * millisecondsPerCell;

                function step(timestamp) {

                    const elapsed = timestamp - startTime;
                    const progress = Math.min(elapsed / duration, 1);

                    let translateX = distanceX * progress;
                    let translateY = distanceY * progress;

                    playerElement.style.transform = `translate(${translateX}px, ${translateY}px)`;

                    markVisitedCells(startPos, endPos, direction, progress);

                    if (progress < 1) {
                        animationFrameId = requestAnimationFrame(step);
                    } else {
                        playerElement.style.transform = '';
                        playerElement.classList.remove('animating');
                        playerElement.style.position = '';
                        playerElement.style.left = '';
                        playerElement.style.top = '';
                        playerElement.style.width = '';
                        playerElement.style.height = '';

                        if (startCell.contains(playerElement)) {
                            startCell.removeChild(playerElement);
                        }

                        endCell.appendChild(playerElement);
                        callback();
                    }
                }

                animationFrameId = requestAnimationFrame(step);
            }


            function markVisitedCells(startPos, endPos, direction, progress) {
                if (playground[endPos.row][endPos.col] === TP) {
                    const totalSteps = Math.max(
                        Math.abs(endPos.row - startPos.row),
                        Math.abs(endPos.col - startPos.col)
                    ) - 1;
                    
                    const currentStep = Math.min(Math.floor(progress * totalSteps), totalSteps);
                    
                    for (let step = 0; step <= currentStep; step++) {
                        let row, col;

                        switch (direction) {
                            case 'up':
                                row = startPos.row - step;
                                col = startPos.col;
                                break;
                            case 'down':
                                row = startPos.row + step;
                                col = startPos.col;
                                break;
                            case 'left':
                                row = startPos.row;
                                col = startPos.col - step;
                                break;
                            case 'right':
                                row = startPos.row;
                                col = startPos.col + step;
                                break;
                        }

                        if (row < 0 || row >= rows || col < 0 || col >= cols) continue;
                        
                        if (playground[row][col] === TP) checkWin();

                        if (playground[row][col] === PATH) {
                playground[row][col] = VISITED;
                const cell = grid.children[row * cols + col];
                cell.classList.add('visited');
                
                // CORRECTION: Ajouter la logique Lego ici avec 'cell' définie
                if (localStorage.getItem('trailColor') === 'image:lego') {
                    const fill = cell.querySelector('.fill');
                    if (fill && !cell.classList.contains('tp')) {
                        fill.style.backgroundImage = 'url("game/sprites/lego.png")';
                        fill.style.backgroundSize = 'cover';
                        fill.style.filter = getRandomLegoFilter();
                    }
                }
            }
        }
    } else {
        // Section sans téléporteur - déjà correcte
        const totalSteps = Math.max(
            Math.abs(endPos.row - startPos.row),
            Math.abs(endPos.col - startPos.col)
        );
                    
                    const currentStep = Math.floor(progress * totalSteps);
                    
                    for (let step = 0; step <= currentStep; step++) {
                        let row, col;

                        switch (direction) {
                            case 'up':
                                row = startPos.row - step;
                                col = startPos.col;
                                break;
                            case 'down':
                                row = startPos.row + step;
                                col = startPos.col;
                                break;
                            case 'left':
                                row = startPos.row;
                                col = startPos.col - step;
                                break;
                            case 'right':
                                row = startPos.row;
                                col = startPos.col + step;
                                break;
                        }

                        if (row < 0 || row >= rows || col < 0 || col >= cols) continue;

            // MÊME MODIFICATION : Ne définir la couleur Lego QUE quand la cellule devient visitée
            if (playground[row][col] === PATH) {
                playground[row][col] = VISITED;
                const cell = grid.children[row * cols + col];
                cell.classList.add('visited');
                
                // Appliquer la couleur Lego UNIQUEMENT à ce moment
                if (localStorage.getItem('trailColor') === 'image:lego') {
                    const fill = cell.querySelector('.fill');
                    if (fill && !cell.classList.contains('tp')) {
                        fill.style.backgroundImage = 'url("game/sprites/lego.png")';
                        fill.style.backgroundSize = 'cover';
                        fill.style.filter = getRandomLegoFilter();
                    }
                }
            }
        }
    }
}
            

            function fallIntoHole(callback) {
                const playerElement = document.querySelector('.player');
                if (!playerElement) return;

                playerElement.classList.add('falling');
                
                setTimeout(() => {
                    if (playerElement.parentNode) {
                        playerElement.parentNode.removeChild(playerElement);
                    }
                    callback();
                }, 500);
            }
            
            function slide(direction) {
                if (gameEnded || inputBlocked || (!isSolverRunning && isAnyDropdownOpen()) || !playerPos) return;

                clearHint();
                if (isMoving) return;
                isMoving = true;
                
                // Check if direction changed, and increment counter if so
                if (previousDirection === null || previousDirection !== direction) {
                    let moves = parseInt(sessionStorage.getItem("moves")) || 0;
                    moves++;
                    sessionStorage.setItem("moves", moves.toString());
                }
                
                // Set the current direction as the previous one for next time
                previousDirection = direction;
                
                let fallen = false;
                let newRow = playerPos.row;
                let newCol = playerPos.col;
                let moved = false;
                const startPos = { ...playerPos };
                let teleportDestination = null;
                
                while (true) {
                    let nextRow = newRow;
                    let nextCol = newCol;

                    switch (direction) {
                        case 'up': nextRow--; break;
                        case 'down': nextRow++; break;
                        case 'left': nextCol--; break;
                        case 'right': nextCol++; break;
                    }

                    if (nextRow < 0 || nextRow >= rows || nextCol < 0 || nextCol >= cols || 
                        playground[nextRow][nextCol] === WALL) {
                        break;
                    }

                    if (playground[nextRow][nextCol] === TP) {
                        const otherTP = TPs.find(tp => !(tp.row === nextRow && tp.col === nextCol));
                        
                        if (otherTP) {
                            teleportDestination = { 
                                source: { row: nextRow, col: nextCol },
                                destination: otherTP
                            };
                            
                            newRow = nextRow;
                            newCol = nextCol;
                            moved = true;
                            break;
                        }
                    }

                    if (playground[nextRow][nextCol] === HOLE) {
                        fallen = true;
                        newRow = nextRow;
                        newCol = nextCol;
                        moved = true;
                        break;
                    }

                    newRow = nextRow;
                    newCol = nextCol;
                    moved = true;
                }   if (moved) {
                    
                    animateMovement(startPos, { row: newRow, col: newCol }, direction, () => {                        
                        if (teleportDestination) {
                            const tpDest = teleportDestination.destination;
                            
                            // Marquer le téléporteur source comme visité
                            const sourceCell = grid.children[newRow * cols + newCol];
                            sourceCell.classList.add('visited');
                            
                            // Marquer le téléporteur de destination comme visité
                            const destCell = grid.children[tpDest.row * cols + tpDest.col];
                            destCell.classList.add('visited');
                            
                            const oldCell = grid.children[newRow * cols + newCol];
                            const newCell = grid.children[tpDest.row * cols + tpDest.col];
                            const player = document.querySelector('.player');
                            
                            if (player && oldCell.contains(player)) {
                                oldCell.removeChild(player);
                                newCell.appendChild(player);
                                
                                // Ajouter cette ligne pour réappliquer le skin après déplacement
                                applyPlayerSkin();
                            }
                            
                            playerPos.row = tpDest.row;
                            playerPos.col = tpDest.col;
                            
                            let afterTPRow = tpDest.row;
                            let afterTPCol = tpDest.col;
                            
                            switch (direction) {
                                case 'up': afterTPRow--; break;
                                case 'down': afterTPRow++; break;
                                case 'left': afterTPCol--; break;
                                case 'right': afterTPCol++; break;
                            }
                            
                            isMoving = false;
                            
                            if (afterTPRow >= 0 && afterTPRow < rows && 
                                afterTPCol >= 0 && afterTPCol < cols && 
                                playground[afterTPRow][afterTPCol] !== WALL)
                            {
                                slide(direction);
                            } else {
                                checkWin();
                            }
                        } 
                        else if (fallen) {
                            fallIntoHole(() => {
                                checkWin();
                                if (!gameEnded){
                                    sessionStorage.setItem("moves", "0");
                                    const niveau = new URLSearchParams(window.location.search).get("level") ?? 1;
                                    document.location.href = `play.php?level=${niveau}`;
                                }
                            });
                        }
                        else {
                            playerPos.row = newRow;
                            playerPos.col = newCol;
                            isMoving = false;
                            checkWin();
                        }
                    });
                } else {
                    isMoving = false;
                }
            }
            
            // function isDropdownMenuOpen() {
            //     const menuToggle = document.getElementById('menu_toggle');
            //     return menuToggle && menuToggle.checked;
            // }

            function checkWin() {
                let NotVisited = playground.some(l => l.some(n => n === PATH));
                // console.log(sessionStorage.getItem("moves"), playground);
                if (!NotVisited) {
                    gameEnded = true; // S'assurer que gameEnded est défini
                    setTimeout(() => {
                        showWinPopup(sessionStorage.getItem("moves"));
                    }, 150);
                }

            }
            
    newGameButton.addEventListener('click', () => {
        if (!isGameActive()) {
            return;
        }
        
        previousDirection = null;
        // Réinitialiser le compteur de mouvements
        sessionStorage.setItem("moves", "0");
        
        // Réinitialiser l'état du jeu
        gameEnded = false;
        isMoving = false;
        solverUsed = false;
        hintUsed = false;
        inputBlocked = false; 
        
        // Réinitialiser la position du joueur et le plateau à l'état original
        playerPos = {...originalPlayerPos};
        playground = JSON.parse(JSON.stringify(originalPlayground));
        
        // Effacer tout indice ou animation en cours
        clearHint();
        
        // Redessiner le plateau de jeu
        initGame();
        
        // Mettre à jour l'affichage des boutons
        updateButtonState();
    });

    if ((performance.navigation.type == performance.navigation.TYPE_RELOAD || performance.navigation.type == performance.navigation.TYPE_NAVIGATE) && !savedState) {
        sessionStorage.setItem("moves", "0");
    }

    document.addEventListener('keydown', (e) => {
        if (inputBlocked || isSolverRunning || isAnyDropdownOpen() || gameEnded) return;
        switch (e.key) {
            case 'ArrowUp': 
                e.preventDefault();
                slide('up'); 
                break;
            case 'ArrowDown': 
                e.preventDefault();
                slide('down'); 
                break;
            case 'ArrowLeft': 
                e.preventDefault();
                slide('left'); 
                break;
            case 'ArrowRight': 
                e.preventDefault();
                slide('right'); 
                break;
            case 'r':
                case 'R':
                    e.preventDefault();
                
                document.getElementById('new-game').click();
                break;
        }
    });

let touchStartX = 0;
let touchStartY = 0;
let touchEndX = 0;
let touchEndY = 0;
const swipeThreshold = 50; // Distance minimale pour un swipe valide (en pixels)

// Configuration des écouteurs tactiles
grid.addEventListener('touchstart', function(e) {
    // Vérification plus spécifique pour le slider
    if (e.target.closest('#volume-slider') || 
        e.target.closest('#sound-dropdown') || 
        isAnyDropdownOpen()) {
        return; // Ne pas traiter les touches sur le slider ou dans les dropdowns
    }
    touchStartX = e.changedTouches[0].screenX;
    touchStartY = e.changedTouches[0].screenY;
}, false);

grid.addEventListener('touchend', function(e) {
    // Vérifier si le touch provient du slider ou du dropdown
    if (e.target.closest('#volume-slider') || 
        e.target.closest('#sound-dropdown') || 
        isAnyDropdownOpen()) {
        return; // Ne pas traiter les touches sur le slider ou dans les dropdowns
    }
    touchEndX = e.changedTouches[0].screenX;
    touchEndY = e.changedTouches[0].screenY;
    handleSwipe();
}, false);

// Empêcher le défilement de page pendant le jeu
grid.addEventListener('touchmove', function(e) {
    if (e.target.closest('#volume-slider') || 
        e.target.closest('#sound-dropdown')) {
        return; // Laisser le comportement normal pour le slider
    }
    // Empêcher le défilement de la page pendant le jeu
    e.preventDefault();
}, { passive: false });

// Fonction pour déterminer la direction du swipe
function handleSwipe() {
    if (isAnyDropdownOpen() || inputBlocked) return;
    const xDiff = touchEndX - touchStartX;
    const yDiff = touchEndY - touchStartY;
    
    // S'assurer que le swipe est suffisamment long
    if (Math.abs(xDiff) < swipeThreshold && Math.abs(yDiff) < swipeThreshold) {
        return; // Swipe trop court, ignorer
    }
    
    // Déterminer si le swipe est plus horizontal que vertical
    if (Math.abs(xDiff) > Math.abs(yDiff)) {
        if (xDiff > 0) {
            // Swipe vers la droite
            slide('right');
        } else {
            // Swipe vers la gauche
            slide('left');
        }
    } else {
        if (yDiff > 0) {
            // Swipe vers le bas
            slide('down');
        } else {
            // Swipe vers le haut
            slide('up');
        }
    }
}

// Fonction pour exécuter la solution automatiquement
function executeMove(direction) {
    return new Promise(resolve => {
        // console.log(`Exécution du mouvement: ${direction}`);
        
        // Débloquer temporairement le jeu
        const savedGameEnded = gameEnded;
        gameEnded = false;
        
        // S'assurer que isMoving est réinitialisé correctement
        if (isMoving) {
            // console.warn("Mouvement précédent toujours en cours, attente...");
            const checkPrevious = setInterval(() => {
                if (!isMoving) {
                    clearInterval(checkPrevious);
                    executeMovement();
                }
            }, 50);
        } else {
            executeMovement();
        }
        
        function executeMovement() {
            // Exécuter le mouvement
            slide(direction);
            
            // Vérifier si le mouvement a démarré
            if (!isMoving) {
                // console.log(`Mouvement ${direction} impossible - obstacle ou mur`);
                gameEnded = savedGameEnded; // Restaurer l'état
                resolve();
                return;
            }
            
            // Attendre que le mouvement soit terminé
            const checkInterval = setInterval(() => {
                if (!isMoving) {
                    // console.log(`Mouvement ${direction} terminé`);
                    clearInterval(checkInterval);
                    gameEnded = savedGameEnded; // Restaurer l'état
                    resolve();
                }
            }, 50);
            
            // // Sécurité en cas de blocage
            // setTimeout(() => {
            //     if (isMoving) {
            //         console.warn(`Timeout pour le mouvement ${direction}`);
            //         isMoving = false;
            //         gameEnded = savedGameEnded;
            //         clearInterval(checkInterval);
            //         resolve();
            //     }
            // }, 3000);
        }
    });
}

    // Configurer le bouton pour exécuter la solution
    const runSolutionButton = document.getElementById('run-solution')
    // console.log(runSolutionButton);
    if (runSolutionButton) {
    runSolutionButton.addEventListener('click', function(e) {
    e.preventDefault(); // Empêche le comportement par défaut

    const hintBtn = document.getElementById('hint-btn');
    if (hintBtn) hintBtn.classList.add('loading');
    
    // Fermer le dropdown des hints
    DropdownMenu.closeAll();
    
    // Vérifier si une solution existe déjà pour CE NIVEAU dans sessionStorage
    const isAtStartPosition = 
        playerPos.row === originalPlayerPos.row && 
        playerPos.col === originalPlayerPos.col;
    
    // Utiliser la solution pré-calculée si le joueur est à la position initiale
    const existingSolution = isAtStartPosition ? sessionStorage.getItem("solution") : null;
    
    if (existingSolution && existingSolution !== "No path found." && existingSolution.length > 0) {
        // Exécuter directement la solution existante pour ce niveau
        // console.log("Utilisation de la solution pré-calculée depuis la base de données");
        executeSolution(existingSolution);
        if (hintBtn) hintBtn.classList.remove('loading');
    } else if (existingSolution === "No path found.") {
        // Aucune solution trouvée - afficher les marqueurs d'erreur
        // console.log("Aucune solution trouvée pour ce niveau");
        markNoSolutionNeighbors();
        if (hintBtn) hintBtn.classList.remove('loading'); 
    } else {
        // Préparer et envoyer la requête pour obtenir la solution
        prepareHintForm('solution');
        
        // Envoyer la requête via fetch au lieu du formulaire
        const formData = new FormData(document.getElementById('hint-form'));
        
        fetch('game/scripts/solveur.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (hintBtn) hintBtn.classList.remove('loading');
            try {
                // Essayer de parser la réponse comme JSON
                const jsonData = JSON.parse(data);
                if (jsonData.solution) {
                    executeSolution(jsonData.solution);
                } else {
                    // console.error("Aucune solution trouvée");
                    markNoSolutionNeighbors();
                }
            } catch (e) {
                // Si ce n'est pas du JSON, traiter comme du texte
                if (data && data !== "No solution found") {
                    executeSolution(data);
                } else {
                    // console.error("Aucune solution trouvée");
                    markNoSolutionNeighbors();
                }
            }
        })
        .catch(error => {
            if (hintBtn) hintBtn.classList.remove('loading');
            // console.error("Erreur lors de la récupération de la solution:", error);
            alert("Error while fetching solution.");
        });
    }
});
}
// Fonction pour exécuter la solution automatiquement (mise à jour)
isSolverRunning = false;
async function executeSolution(solution) {
    // console.log("Début de l'exécution automatique de la solution:", solution);
    
    // Vérifier explicitement si la solution est "No path found."
    if (!solution || solution === "No path found." || solution.trim().startsWith("No solution found")) {
        // console.log("Aucune solution trouvée");
        markNoSolutionNeighbors();
        isSolverRunning = false;
        updateButtonState();
        return;
    }

    isSolverRunning = true;
    solverUsed = true;
    updateButtonState();
    // Sauvegarder l'état du jeu
    const savedGameEnded = gameEnded;
    
    try {
        // console.log("Préparation de l'exécution - état du jeu:", {
        //     playerPos: {...playerPos},
        //     gameEnded: gameEnded,
        //     isMoving: isMoving
        // });
        
        for (let i = 0; i < solution.length; i++) {
            const char = solution[i];
            // console.log(`Mouvement ${i+1}/${solution.length}: ${char}`);
            
            // Convertir le caractère en direction
            let direction;
            switch (char) {
                case 'N': direction = 'up'; break;
                case 'S': direction = 'down'; break;
                case 'E': direction = 'right'; break;
                case 'O': case 'W': direction = 'left'; break;
                default: 
                    // console.warn(`Direction inconnue: ${char}`);
                    continue;
            }
            
            // Exécuter le mouvement
            await executeMove(direction);
            
            // Attendre un peu entre chaque mouvement
            await new Promise(resolve => setTimeout(resolve, 300));
            
            // Vérifier si le jeu est terminé
            if (document.getElementById('win-popup').style.display !== 'none') {
                // console.log("Niveau terminé !");
                break;
            }
        }
    } catch (error) {
        console.error("Erreur pendant l'exécution de la solution:", error);
    } finally {
        // Restaurer l'état du jeu
        gameEnded = savedGameEnded;
        isSolverRunning = false;
        updateButtonState();
        // console.log("Fin de l'exécution automatique");
    }
}



function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

    // Configurer le bouton pour afficher un conseil
    const tipsButton = document.getElementById('tips-giving');
    if (tipsButton) {
        tipsButton.addEventListener('click', showTip);
    }

});



function prepareHintForm(mode) {
    // Create a clean state representation
    const currentPlaygroundFlat = [];
    
    // console.log("Current playground state:", playground);
    // console.log("Player position:", playerPos);

    // Clear all visited positions first
    let cleanGrid = [];
    for (let row = 0; row < rows; row++) {
        cleanGrid[row] = [];
        for (let col = 0; col < cols; col++) {
            // Keep walls, teleporters and holes as they are
            if (playground[row][col] === WALL || 
                playground[row][col] === TP || 
                playground[row][col] === HOLE) {
                cleanGrid[row][col] = playground[row][col];
            } 
            // Mark visited cells as VISITED (0)
            else if (playground[row][col] === VISITED) {
                cleanGrid[row][col] = VISITED;
            }
            // Mark all other cells as PATH (1)
            else if (playground[row][col] === PATH) {
                cleanGrid[row][col] = PATH;
            }
            else {
                cleanGrid[row][col] = EMPTY;
            }
        }
    }
    
    // Flatten the grid
    for (let row = 0; row < rows; row++) {
        for (let col = 0; col < cols; col++) {
            currentPlaygroundFlat.push(cleanGrid[row][col]);
        }
    }
    
    // Send the current position
    document.getElementById('statesaved').value = JSON.stringify([playerPos.row, playerPos.col]);
    
    // Send the current game state
    const stateToSend = [rows, cols, ...currentPlaygroundFlat];
    document.getElementById('currentState').value = JSON.stringify(stateToSend);
    document.getElementById('hint-mode').value = mode;
    
    // console.log("State sent to solver:", {
    //     grid_dimensions: [rows, cols],
    //     player_pos: [playerPos.row, playerPos.col],
    //     visited_cells: currentPlaygroundFlat.filter(cell => cell === VISITED).length,
    //     playground :  stateToSend,
    //     remaining_paths: currentPlaygroundFlat.filter(cell => cell === PATH).length
    // });
}

    // Fonction pour afficher un indice visuel
    function showVisualHint() {
        const solution = sessionStorage.getItem("solution");
    if (!solution || solution === "No solution found" || solution.trim().startsWith("No solution")) {
        // Si aucune solution n'est trouvée, afficher les croix rouges
        markNoSolutionNeighbors();
        return;
    }

        clearHint();
        hintUsed = true;

        // Trouver le prochain mouvement possible
        const nextMove = solution[0];
        const directions = {
            'N': { row: -1, col: 0 },
            'S': { row: 1, col: 0 },
            'E': { row: 0, col: 1 },
            'O': { row: 0, col: -1 }
        };

        const direction = directions[nextMove];
        let currentRow = playerPos.row;
        let currentCol = playerPos.col;

        // Marquer les cellules dans la direction du mouvement
        while (true) {
            currentRow += direction.row;
            currentCol += direction.col;

            if (currentRow < 0 || currentRow >= rows || 
                currentCol < 0 || currentCol >= cols || 
                playground[currentRow][currentCol] === WALL || 
                playground[currentRow][currentCol] === HOLE) {
                break;
            }

            highlightedCells.push({ row: currentRow, col: currentCol });
            const cell = document.querySelector(`.cell[data-row="${currentRow}"][data-col="${currentCol}"]`);
            if (cell) {
                cell.classList.add('hint-highlight');
            }
            // Arrêter le clignotement si on atteint un téléporteur
            if (playground[currentRow][currentCol] === TP) {
                break;
        }
        }

        // Configurer le timeout pour retirer le surlignage
        hintTimeout = setTimeout(clearHint, 3000);
    }

    // Fonction pour effacer l'indice visuel
    function clearHint() {
        if (hintTimeout) {
            clearTimeout(hintTimeout);
            hintTimeout = null;
        }
        
        highlightedCells.forEach(pos => {
            const cell = document.querySelector(`.cell[data-row="${pos.row}"][data-col="${pos.col}"]`);
            if (cell) {
                cell.classList.remove('hint-highlight');
            }
        });
        
        highlightedCells = [];
    }
                // 1. Vérification des sélecteurs
    const homeLogoLink = document.querySelector('.left-wrapper a');
    const homeLogoImg = document.querySelector('.left-wrapper a img[alt="Home"]');
    const grid = document.querySelector('.grid');
    const levelMenu = document.getElementById('level-menu');
    const closeBtn = document.getElementById('close-level-menu');
    const controls = document.querySelector('.controls');
    

    // console.log('Éléments trouvés:', {
    //     homeLogoLink,
    //     homeLogoImg, 
    //     grid,
    //     levelMenu,
    //     closeBtn
    // });


    // 3. Gestion du bouton Annuler
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            if (levelMenu) levelMenu.style.display = 'none';
            if (grid) grid.style.display = '';
            
        });
    }

    // 4. Gestion des boutons de niveau
    document.querySelectorAll('.level-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (levelMenu) levelMenu.stylxe.display = 'none';
            if (grid) grid.style.display = '';
            
        });
    });

// Gestion des skins
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // AJOUTER ICI : Définir le titre par défaut dès le début
    const headerTitle = document.querySelector('.controls .title');
    if (headerTitle && !urlParams.has('level')) {
        headerTitle.textContent = 'Main Menu';
    }
    
    if (urlParams.has('level') && (savedState || sessionStorage.getItem("inGame"))) {
        // On est en jeu, afficher le nom du niveau
        const headerTitle = document.querySelector('.controls .title');
        if (headerTitle) {
            const levelName = sessionStorage.getItem("levelName");
            if (levelName) {
                headerTitle.textContent = levelName;
            } else {
                const levelNumber = urlParams.get('level') || "1";
                headerTitle.textContent = `Level ${levelNumber}`;
            }
        }
    } else if (!urlParams.has('level') || (!savedState && !sessionStorage.getItem("inGame"))) {
        // Afficher le menu d'accueil
        // console.log('Affichage du menu d\'accueil au chargement initial');
        
        const headerTitle = document.querySelector('.controls .title');
        if (headerTitle) {
            headerTitle.textContent = menuTitles['home-menu'];
        }
        MenuManager.showMenu('home-menu');
        sessionStorage.setItem("inGame", "true");
    }

    updateButtonState();
    updateLeaderboardButtonState();
    
    // Récupérer les skins sauvegardés ou utiliser les valeurs par défaut
    let currentPlayerSkin = localStorage.getItem('playerSkin') || 'default';
    let currentTrailColor = localStorage.getItem('trailColor') || 'pink';
    
    // Appliquer les skins sauvegardés au chargement
    applySkins(currentPlayerSkin, currentTrailColor);
    
    // Sélection d'un skin de joueur
    document.querySelectorAll('.player-skin').forEach(option => {
        option.addEventListener('click', function() {
            // Supprimer la sélection précédente
            document.querySelectorAll('.player-skin').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Sélectionner le nouveau skin
            this.classList.add('selected');
            currentPlayerSkin = this.dataset.skin;
        });
    });
    
    // Sélection d'une couleur de traînée
    document.querySelectorAll('.trail-skin').forEach(option => {
        option.addEventListener('click', function() {
            // Supprimer la sélection précédente
            document.querySelectorAll('.trail-skin').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Sélectionner la nouvelle couleur
            this.classList.add('selected');
            currentTrailColor = this.dataset.color;
        });
    });
    
    // Appliquer les skins sélectionnés
document.querySelector('.apply-skins-btn').addEventListener('click', function() {
    applySkins(currentPlayerSkin, currentTrailColor);
    
    // Sauvegarder les choix
    localStorage.setItem('playerSkin', currentPlayerSkin);
    localStorage.setItem('trailColor', currentTrailColor);
    
    // Au lieu de fermer les menus, afficher le menu home
    MenuManager.showMenu('home-menu');
    
    // Initialiser les sélections dans l'interface
    highlightSelectedSkins();
});

});

// Appliquer les skins au jeu
function applySkins(playerSkin, trailColor) {
    // Appliquer le skin du joueur
    applyPlayerSkin(playerSkin);
    
    let skinStyleElement = document.getElementById('skin-styles');
    if (!skinStyleElement) {
        skinStyleElement = document.createElement('style');
        skinStyleElement.id = 'skin-styles';
        document.head.appendChild(skinStyleElement);
    }

    if (trailColor === 'image:lego') {
        localStorage.setItem('trailColor', 'image:lego');
        skinStyleElement.textContent = `
        .cell.visited .fill { 
            background-image: url("game/sprites/lego.png") !important;
            background-size: cover !important;
            background-position: center !important;
            transform: scale(1); 
            display: flex;
            z-index: 1;
        }
        .cell.tp.visited {
            background-size: cover !important;
            background-repeat: no-repeat !important;
            position: relative !important;
        }
        .cell.tp.visited .fill {
            background-color: transparent !important;
        }
    `;
    
    document.querySelectorAll('.cell.visited .fill').forEach(fill => {
        if (!fill.closest('.cell').classList.contains('tp')) {
            fill.style.backgroundImage = 'url("game/sprites/lego.png")';
            fill.style.backgroundSize = 'cover';
            fill.style.filter = getRandomLegoFilter();
        }
    });
        setupLegoObserver();
        return; 
    }

    const legoObserver = window._legoObserver;
    if (legoObserver) {
        legoObserver.disconnect();
        window._legoObserver = null;
    }
    
    // Vérifier si c'est une image ou une couleur
    if (trailColor && trailColor.startsWith('image:')) {
        // C'est une image
        const imageName = trailColor.split(':')[1];
        skinStyleElement.textContent = `
            .cell.visited .fill { 
                background-color: transparent !important;
                background-image: url("game/sprites/${imageName}.png") !important;
                background-size: cover !important;
                background-repeat: no-repeat !important;
                background-position: center !important;
                transform: scale(1); 
                display: flex;
                z-index: 1;
            }
            /* Règle modifiée pour les téléporteurs visités */
            .cell.tp.visited {
                background-color: transparent !important;
                position: relative;
            }
            .cell.tp.visited::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-size: cover !important;
                background-repeat: no-repeat !important;
                z-index: 2;
                pointer-events: none;
            }
            /* Fond pour les téléporteurs visités */
            .cell.tp.visited .fill {
                background-color: transparent !important;
                background-image: url("game/sprites/${imageName}.png") !important;
                opacity: 1 !important;
                z-index: 1;
            }
        `;
        
        // Mettre à jour immédiatement les cellules visitées existantes
        document.querySelectorAll('.cell.visited .fill').forEach(fill => {
            fill.style.backgroundColor = 'transparent';
            fill.style.backgroundImage = `url("game/sprites/${imageName}.png")`;
            fill.style.backgroundSize = 'cover';
            fill.style.backgroundPosition = 'center';
        });
    } else {
        // C'est une couleur normale
        skinStyleElement.textContent = `
            .cell.visited .fill { 
                background-color: ${trailColor} !important;
                background-image: none !important;
                transform: scale(1); 
                display: flex; 
            }
            /* Règle modifiée pour les téléporteurs visités */
            .cell.tp.visited {
                background-size: cover;
                background-repeat: no-repeat;
                background-color: ${trailColor} !important;
            }
            /* Garder le fond du téléporteur transparent */
            .cell.tp.visited .fill {
                background-color: transparent !important;
                background-image: none !important;
            }
        `;
        
        // Mettre à jour immédiatement les cellules visitées existantes
        document.querySelectorAll('.cell.visited .fill').forEach(fill => {
            if (!fill.closest('.cell').classList.contains('tp')) {
                fill.style.backgroundColor = trailColor;
                fill.style.backgroundImage = 'none';
            }
        });
    }
    
    // Mettre à jour les téléporteurs visités
    document.querySelectorAll('.cell.tp.visited').forEach(cell => {
        if (trailColor.startsWith('image:')) {
            cell.style.backgroundColor = 'transparent';
        } else {
            cell.style.backgroundColor = trailColor;
        }
    });
}


// Function to get random Lego-like bright colors
function getRandomLegoFilter() {
    const filters = [
        'brightness(1.02) saturate(0.98)',                      // Jaune original #FFCD03
        'brightness(1) saturate(2) hue-rotate(-40deg)',    // Rouge #DC1920
        'brightness(1.3) saturate(0.6) hue-rotate(285deg)',     // Rose #F6ACCD
        'brightness(0.65) saturate(1.7) hue-rotate(160deg)',    // Bleu #006CB7
        'brightness(0.7) saturate(1.4) hue-rotate(100deg)',      // Vert #00AF4C
    ];
    return filters[Math.floor(Math.random() * filters.length)];
}

// Set up observer to monitor cells becoming visited
const legoFilters = {};
function setupLegoObserver() {
    if (window._legoObserver) {
        window._legoObserver.disconnect();
    }
    // Create mutation observer to detect class changes
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.type === 'attributes' && 
                mutation.attributeName === 'class' &&
                mutation.target.classList.contains('cell') &&
                mutation.target.classList.contains('visited')) {
                
                // Vérifier que nous sommes toujours en mode Lego
                if (localStorage.getItem('trailColor') === 'image:lego') {
                    const fill = mutation.target.querySelector('.fill');
                    if (fill && !mutation.target.classList.contains('tp')) {
                        // Appliquer l'image Lego avec un NOUVEAU filtre à chaque fois
                        fill.style.backgroundImage = 'url("game/sprites/lego.png")';
                        fill.style.backgroundSize = 'cover';
                        
                        // TOUJOURS générer un nouveau filtre aléatoire, sans vérifier s'il existe déjà
                        fill.style.filter = getRandomLegoFilter();
                    }
                }
            }
        });
    });
    
    // Observer toutes les cellules pour les changements de classe
    document.querySelectorAll('.cell').forEach(cell => {
        observer.observe(cell, { attributes: true });
    });
    window._legoObserver = observer;
}



// Ajouter cette nouvelle fonction pour appliquer uniquement le skin du joueur
function applyPlayerSkin(playerSkin = null) {
    // Si aucun skin n'est spécifié, utiliser celui sauvegardé dans localStorage
    if (playerSkin === null) {
        playerSkin = localStorage.getItem('playerSkin') || 'default';
    }
    
    // Appliquer le skin à tous les éléments .player actuels
    const playerElements = document.querySelectorAll('.player');
    playerElements.forEach(player => {
        // Réinitialiser les filtres
        player.style.filter = '';
        
        // Appliquer le nouveau filtre selon le skin
        switch (playerSkin) {
            case 'blue':
                player.style.filter = 'hue-rotate(210deg)';
                break;
            case 'red':
                player.style.filter = 'hue-rotate(0deg) saturate(1.7)';
                break;
            case 'green':
                player.style.filter = 'hue-rotate(120deg) saturate(1.3)';
                break;
            case 'yellow':
                player.style.filter = 'hue-rotate(150deg) saturate(20)';
                break;
            case 'cyan':
                player.style.filter = 'hue-rotate(-90deg) saturate(7)';
                break;
            case 'orange':
                player.style.filter = 'hue-rotate(120deg) saturate(2)';
                break;
            case 'pink':
                player.style.filter = 'hue-rotate(25deg) saturate(2.2) brightness(1.1)';
                break;
            case 'fallguys':
                player.style.backgroundImage = 'url("game/sprites/fallguys.png")';
                break;
            case 'toad':
                player.style.backgroundImage = 'url("game/sprites/toad.png")';
                break;
            case 'creeper':
                player.style.backgroundImage = 'url("game/sprites/creeper.png")';
                break;
            case 'cat':
                player.style.backgroundImage = 'url("game/sprites/cat.png")';
                break;
            case 'depressed':
                player.style.backgroundImage = 'url("game/sprites/depressed.png")';
                break;
            case 'kirby':
                player.style.backgroundImage = 'url("game/sprites/kirby.png")';
                break;
            case 'lucky-block':
                player.style.backgroundImage = 'url("game/sprites/lucky-block.png")';
                break;
            case 'skull':
                player.style.backgroundImage = 'url("game/sprites/skull.png")';
                break;
            case 'cool-guy':
                player.style.backgroundImage = 'url("game/sprites/cool-guy.png")';
                break;
            case 'snake':
                player.style.backgroundImage = 'url("game/sprites/snake.png")';
                break;
            case 'smiley':
                player.style.backgroundImage = 'url("game/sprites/smiley.png")';
                break;
            default:
                // Skin par défaut, aucun filtre
                break;
        }
    });
}

// Mettre en évidence les skins actuellement sélectionnés dans le menu
function highlightSelectedSkins() {
    const playerSkin = localStorage.getItem('playerSkin') || 'default';
    const trailColor = localStorage.getItem('trailColor') || 'pink';
    
    // Sélectionner le skin du joueur
    document.querySelectorAll('.player-skin').forEach(option => {
        if (option.dataset.skin === playerSkin) {
            option.classList.add('selected');
        } else {
            option.classList.remove('selected');
        }
    });
    
    // Sélectionner la couleur de traînée
    document.querySelectorAll('.trail-skin').forEach(option => {
        if (option.dataset.color === trailColor) {
            option.classList.add('selected');
        } else {
            option.classList.remove('selected');
        }
    });
}

// Système de gestion des niveaux débloqués
const LevelManager = {
    // Clé utilisée pour stocker les niveaux débloqués dans localStorage
    storageKey: 'unlockedLevels',
    
    // Initialiser les niveaux débloqués
    init: function() {
        // Si aucun niveau n'est débloqué, débloquer uniquement le niveau 1
        if (!localStorage.getItem(this.storageKey)) {
            localStorage.setItem(this.storageKey, JSON.stringify([1]));
        }
        
        this.updateLevelButtons();
    },
    
    // Vérifier si un niveau est débloqué
    isLevelUnlocked: function(level) {
        const unlockedLevels = this.getUnlockedLevels();
        return unlockedLevels.includes(parseInt(level));
    },
    
    // Obtenir la liste des niveaux débloqués
    getUnlockedLevels: function() {
        const unlockedLevels = localStorage.getItem(this.storageKey);
        return unlockedLevels ? JSON.parse(unlockedLevels) : [1];
    },
    
    // Débloquer un niveau
    unlockLevel: function(level) {
        level = parseInt(level);
        const unlockedLevels = this.getUnlockedLevels();
        
        // Ne rien faire si le niveau est déjà débloqué
        if (unlockedLevels.includes(level)) {
            return;
        }
        
        // Ajouter le niveau à la liste des niveaux débloqués
        unlockedLevels.push(level);
        localStorage.setItem(this.storageKey, JSON.stringify(unlockedLevels));
        
        // Mettre à jour les boutons de niveau
        this.updateLevelButtons();
    },
    
    // Mettre à jour les boutons de niveau en fonction des niveaux débloqués
    updateLevelButtons: function() {
        const unlockedLevels = this.getUnlockedLevels();
        
        // Retirer tous les écouteurs d'événements existants
        document.querySelectorAll('.level-btn').forEach(btn => {
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
        });
        
        // Mettre à jour tous les boutons de niveau et ajouter les écouteurs uniquement aux niveaux débloqués
        document.querySelectorAll('.level-btn').forEach(btn => {
            const level = parseInt(btn.dataset.level);
            
            if (unlockedLevels.includes(level)) {
                // Niveau débloqué
                btn.classList.remove('locked');
                btn.classList.add('unlocked');
                btn.textContent = level.toString();  // Retirer le cadenas
                
                // Ajouter l'écouteur d'événement uniquement aux niveaux débloqués
                btn.addEventListener('click', function() {
                    // Ignorer si c'est une flèche de navigation
                    MenuManager.closeMenus();
                    window.location.href = `play.php?level=${level}`;
                });
            } else {
                // Niveau verrouillé
                btn.classList.remove('unlocked');
                btn.classList.add('locked');
                btn.textContent = level.toString() + ' 🔒';  // Ajouter le cadenas
                // Pas d'écouteur d'événement pour les niveaux verrouillés
            }
        });

        this.updateArrowVisibility(unlockedLevels);
    },
    updateArrowVisibility: function(unlockedLevels) {
        const maxUnlockedLevel = Math.max(...unlockedLevels);
        
        // CORRECTION : Flèche droite du menu 1 : visible si niveau 10+ est débloqué OU TOUJOURS visible pour permettre l'exploration
        const arrowRight1 = document.getElementById('arrow-right-1');
        if (arrowRight1) {
            // Modifier la condition pour toujours afficher la flèche, mais vous pouvez la conditionner selon vos besoins
            arrowRight1.style.visibility = 'visible'; // ou (maxUnlockedLevel >= 1) pour toujours visible
            // Ou bien : arrowRight1.style.visibility = (maxUnlockedLevel >= 1) ? 'visible' : 'hidden';
        }
        
        // Flèche droite du menu 2 : visible si niveau 20+ est débloqué
        const arrowRight2 = document.getElementById('arrow-right-2');
        if (arrowRight2) {
            arrowRight2.style.visibility = (maxUnlockedLevel >= 11) ? 'visible' : 'hidden'; // Changé de 20 à 11
        }
    }
};

// Initialiser le gestionnaire de niveaux lors du chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    LevelManager.init();
    
    // Modifier le gestionnaire de clic pour les boutons de niveau
document.querySelectorAll('.level-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Ignorer si c'est une flèche de navigation
        if (this.querySelector('ion-icon')) {
            return;
        }
        
        const level = this.dataset.level;
        
        // Vérifier si le niveau est débloqué
        if (LevelManager.isLevelUnlocked(level)) {
            // Couvrir la grille de jeu avec un overlay blanc
            const grid = document.getElementById('grid');
            
            // Créer un overlay qui couvre la grille
            const gridOverlay = document.createElement('div');
            gridOverlay.style.position = 'absolute';
            gridOverlay.style.top = '0';
            gridOverlay.style.left = '0';
            gridOverlay.style.width = '100%';
            gridOverlay.style.height = '100%';
            gridOverlay.style.backgroundColor = 'white';
            gridOverlay.style.zIndex = '10'; 
            
            // Ajouter l'overlay à la grille
            if (grid) {
                // S'assurer que la grille a une position relative ou absolute pour le positionnement
                grid.style.position = 'relative'; 
                grid.appendChild(gridOverlay);
            }
            
            // Rediriger après un court délai
            setTimeout(() => {
                window.location.href = `play.php?level=${level}`;
            }, 50);
        } else {
            // Niveau verrouillé, afficher un message
            alert("This level is locked! First complete previous level.");
        }
    });
});
    
    // Débloquer le niveau suivant lorsqu'un niveau est terminé
    // (à ajouter dans la fonction qui gère la fin du niveau)
});
    </script>
    <script>
       
// Script pour charger dynamiquement les niveaux personnalisés
function getDifficultyLabel(minmoves) {
    if (minmoves < 6) return '<span class="difficulty-easy">Easy</span>';
    if (minmoves < 10) return '<span class="difficulty-medium">Medium</span>';
    if (minmoves < 15) return '<span class="difficulty-hard">Hard</span>';
    if (minmoves < 25) return '<span class="difficulty-insane">Insane</span>';
    return '<span class="difficulty-demon">Demon</span>';
}

function loadCustomLevels() {
    fetch('php/get_custom_levels.php')
        .then(res => res.json())
        .then(levels => {
            const tbody = document.getElementById('custom-levels-tbody');
            tbody.innerHTML = '';
            if (!levels.length) {
                tbody.innerHTML = '<tr><td colspan="4">Aucun niveau personnalisé.</td></tr>';
                return;
            }
            levels.forEach(level => {
                const tr = document.createElement('tr');
                tr.className = 'table-row';
                tr.innerHTML = `
                    <td class="level-name">${level.name}</td>
                    <td class="creator">${level.creator || "Anonymous"}</td>
                    <td class="difficulty">${getDifficultyLabel(level.minmoves)}</td>
                    <td class="actions">
                        <button class="play-level-btn" data-level="${level.id}">Play</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            // Ajoute l'action sur les boutons Play
            tbody.querySelectorAll('.play-level-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    window.location.href = `play.php?level=${this.dataset.level}`;
                });
            });
        })
        .catch(() => {
            const tbody = document.getElementById('custom-levels-tbody');
            tbody.innerHTML = '<tr><td colspan="4">Erreur de chargement.</td></tr>';
        });
}

// Appelle la fonction quand on ouvre le menu custom levels
document.getElementById('custom-levels').addEventListener('click', function() {
    MenuManager.showMenu('custom-levels-menu');
    loadCustomLevels();
});
        
    </script>
    </main>
    <footer>
        <?php include 'php/footer.php'; ?>
    </footer>
</body>
<script type="module">
    import { generate } from './game/scripts/generator.js';
    
    // Make it available globally immediately
    window.generateLevel = generate;
    
    document.addEventListener('DOMContentLoaded', () => {
    // Set up the generate button functionality
    const generateButton = document.getElementById('generate-random-level');
    if (generateButton) {
        // Add click event handler
        generateButton.addEventListener('click', async function() {
            // Créer une fonction de réinitialisation du bouton pour éviter la duplication de code
            const resetButton = () => {
                clearInterval(loadingInterval);
                this.textContent = "Generate";
                this.disabled = false;
            };

            // Fonction pour générer un niveau avec timeout
            const generateWithTimeout = async (difficulty, attemptCount = 1) => {
                // Limiter le nombre de tentatives silencieuses à 3 pour éviter une boucle infinie
                if (attemptCount > 3) {
                    throw new Error("Unable to generate a valid level after multiple attempts");
                }

                return new Promise(async (resolve, reject) => {
                    // Créer un timeout qui relancera automatiquement la génération
                    const timeoutId = setTimeout(() => {
                        console.log(`Generation timeout (attempt ${attemptCount}), retrying silently...`);
                        // Annuler cette tentative et en démarrer une nouvelle
                        resolve(generateWithTimeout(difficulty, attemptCount + 1));
                    }, 10000); // 10 secondes

                    try {
                        const result = await generate(difficulty);
                        clearTimeout(timeoutId);
                        resolve(result);
                    } catch (error) {
                        clearTimeout(timeoutId);
                        reject(error);
                    }
                });
            };

            // Afficher l'état de chargement
            this.disabled = true;
            let dotCount = 0;
            const loadingInterval = setInterval(() => {
                dotCount = (dotCount + 1) % 4;
                const dots = '.'.repeat(dotCount);
                this.textContent = `Generating${dots}${' '.repeat(3-dotCount)}`;
            }, 300);

            try {
                // Vérifier la difficulté sélectionnée
                const selectedRadio = document.querySelector('input[name="difficulty"]:checked');
                if (!selectedRadio) {
                    resetButton();
                    return alert("Please select a difficulty level");
                }
                const difficulty = parseInt(selectedRadio.value);
                
                // Générer le niveau avec le timeout automatique
                const gen = await generateWithTimeout(difficulty);
                
                if (!gen || !gen.playground || !gen.solution) {
                    resetButton();
                    return alert("An error occurred while generating level.");
                }

                const levelData = {
                    data: JSON.parse(gen.playground),
                    solution: gen.solution,
                    minmoves: gen.solution.length,
                    name: `Random Level (${getDifficultyName(difficulty)})`
                };
                
                const response = await fetch("game/scripts/upload.php", {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify(levelData)
                });
                
                const result = await response.json();
                
                if (!result.success) throw new Error(result.message || "Error while uploading level");

                // Redirection en cas de succès
                window.location.href = `play.php?level=${result.levelId}`;

            } catch (error) {
                // console.error("Generation error:", error);
                resetButton();
                alert("Failed to generate level: " + error.message + " Please try again.");
            } finally {
                // S'assurer que le bouton est toujours réinitialisé si la redirection échoue
                if (this.disabled) {
                    resetButton();
                }
            }
        });
    }
    
    function getDifficultyName(difficulty) {
        const difficulties = {
            0: "Easy",
            1: "Medium", 
            2: "Hard",
            3: "Insane",
            4: "Demon"
        };
        return difficulties[difficulty] || "Unknown";
    }
});
</script>
</html>