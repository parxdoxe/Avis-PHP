<?php

require 'config/db.php';

session_start();


$name = $_POST['name'] ?? null;
$review = $_POST['review'] ?? null;
$note = $_POST['note'] ?? null;
$date = date('Y-m-d H:i:s') ?? null;
$image = $_FILES['file'] ?? null;
$errors = [];
$success = false;




if (!empty($_POST)) {
    

    if (empty($name)) {
        $errors['name'] = 'Votre nom est requis.';
    }
    if (empty($review)) {
        $errors['review'] = 'Votre commentaire est requis.';
    }
    if (empty($name)) {
        $errors['note'] = 'Votre note doit être comprise entre 1 et 5.';
    }

   
    $mime = !empty($image['tmp_name']) ? mime_content_type($image['tmp_name']) : '';
    $mimeTypes = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
    
    



    if (empty($errors)) {


        $file = $image['tmp_name'];
        $filename = $image['name'];

        $path = pathinfo($filename);

       if ($filename) {
        
           $filename = $path['filename'].'-'.uniqid().'.'.$path['extension'];
       }


        if (!is_dir('uploads')) {
            mkdir('uploads');
        }

        if (in_array($mime, $mimeTypes)) {  
            move_uploaded_file($file, 'uploads/'.$filename);
        } else {
            $errors['image'] = 'Le fichier n\'est pas une image.';
        }


        $query = $db->prepare('INSERT INTO review (name, review, note, created_at, image)
        VALUES (:name, :review, :note, :created_at, :image)');
        $success = $query->execute([
            ':name' => $name,
            ':review' => $review,
            ':note' => $note,
            ':created_at' => $date,
            ':image' => $filename,
        ]);
    }
}

// Tous les commentaires
$query = $db->prepare('SELECT * FROM review');
$query->execute();
$notes = $query->fetchAll();

// Select notes
$query = $db->prepare('SELECT note FROM review');
$query->execute();
$avis = $query->fetchAll();


// Moyenne
$query = $db->prepare('SELECT AVG(note) as moyenneNote FROM review');
$query->execute();
$moyenne = $query->fetch();

// Somme des notes
$query = $db->prepare('SELECT SUM(note = 5) as note5, SUM(note = 4) as note4, SUM(note = 3) as note3, SUM(note = 2) as note2, SUM(note = 1) as note1 FROM review');
$query->execute();
$test = $query->fetch();





?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
</head>

<body class="container mx-auto h-full flex items-center flex-col">
    
    <div class="flex justify-between items-center w-4/6 mb-5 mt-5">

    <h1 class="text-2xl uppercase font-medium">Restaurant</h1>

    <?php if (!empty($_SESSION['name'])) { ?>
        <div class="flex items-center justify-center">
            <a class="text-blue-500 hover:text-blue-300 mr-5" href="logout.php?logout=1"><?= $_SESSION['name'] ?></a>
            <a href="logout.php?logout=1"><img src="https://static.vecteezy.com/ti/vecteur-libre/t2/2002403-homme-avec-barbe-avatar-personnage-icone-isole-gratuit-vectoriel.jpg" class="rounded-full w-20" alt="Avatar"/></a>
        </div>
    <?php } else if (!empty($name)) { ?>
    <a class="text-blue-500 hover:text-blue-300" href="login.php?name=<?= $name ?> ">Se connecter</a>
  <?php } else { ?>
        <a class="text-blue-500 hover:text-blue-300" href="avis.php">Se connecter</a>
   <?php } ?>
    
    

    </div>



    <!-- Notre moyenne -->
    <div class="flex justify-center w-4/6 mb-5">
        <div class="block rounded-lg shadow-lg bg-white border border-gray-300 w-full">
            <div class="py-3 px-6 border-b border-gray-300">
                Notre moyenne :
            </div>
            <div class="p-5 flex justify-around items-center">

                <div class="text-center">
                <h2 class="text-amber-400 text-2xl mb-2">
                        
                            <?php if ($moyenne['moyenneNote'] === null) { ?>
                                <span>0</span>
                           <?php } else { ?>
                                <span><?= round($moyenne['moyenneNote'] ?? '') ?></span>
                            <?php } ?>  
                        
                        <span>/</span> 5</h2> 
                    <?php for ($i=1; $i <= 5 ; $i++) { ?>

                        <?php if ($moyenne['moyenneNote'] === null) { ?>
                            <span class="text-gray-400">&#9733;</span>
                        <?php }  else if (round($moyenne['moyenneNote'] ?? '') % $i < round($moyenne['moyenneNote'] ?? '')) { ?>
                            <span class="text-amber-400">&#9733;</span>
                       <?php } else { ?>
                                <span class="text-gray-400">&#9733;</span> 
                      <?php } ?>
                        
                    <?php } ?>           
                    <h4 class="text-xl font-medium mt-2"><?= count($avis) ?></h4>
                </div>
                
                <div class="w-1/3 flex flex-col">
                <?php for ($i=5; $i >= 1; $i--) { ?>

                        <div class="flex justify-center items-center mb-2 ">
                            <p><?= $i; ?> <span class="text-amber-400">&#9733;</span></p>
                            <div class="w-4/5 mx-1  bg-gray-200 h-5 rounded-xl ">
                               
                            <?php if ($i === 5) { ?>
                                <div class="bg-amber-300 h-5 rounded-xl w-[<?=$test['note5'] * 10 ?>%]"></div>
                           <?php } else if ($i === 4) { ?>
                                <div class="bg-amber-300 h-5 rounded-xl w-[<?=$test['note4'] * 10 ?>%]"></div>
                          <?php } else if ($i === 3) { ?>
                                <div class="bg-amber-300 h-5 rounded-xl w-[<?=$test['note3'] * 10 ?>%]"></div>
                          <?php } else if ($i === 2) { ?>
                                <div class="bg-amber-300 h-5 rounded-xl w-[<?=$test['note2'] * 10 ?>%]"></div>
                          <?php } else { ?>
                             <div class="bg-amber-300 h-5 rounded-xl w-[<?=$test['note1'] * 10 ?>%]"></div>
                         <?php } ?>
                             
                             
                             
                                    
                            </div>
                                <p>
                                    (<?php if ($i === 5) { ?>
                                        <?= $test['note5'] ?>
                                    <?php } else if ($i === 4) { ?>
                                        <?= $test['note4'] ?>
                                    <?php } else if ($i === 3) { ?>
                                        <?= $test['note3'] ?>
                                    <?php } else if ($i === 2) { ?>
                                        <?= $test['note2'] ?>
                                    <?php } else { ?>
                                        <?= $test['note1'] ?>
                                    <?php } ?>)
                                </p>                            
                        </div>
                        <?php } ?>
                    </div>
                
                <div class="flex flex-col items-center justify-center">
                    <h5 class="text-gray-900 text-xl font-medium mb-2">Laissez votre avis</h5>
                    <a href="#reviewP" class=" inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">Noter</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Publier un avis -->
    <?php if ($success) { ?>
            <h2 class="text-green-500">Le commentaire est posté.</h2>
            <?php if (empty($_SESSION['name'])) { ?>
                <h2 class="text-gray-500">Vous pouvez vous connecter.</h2>
           <?php } else if(!empty($_SESSION['name'])) { ?>

          <?php } ?>
    <?php } ?>
    <?php if(!$success) { ?>
    <div class="flex justify-center w-4/6">
        <div class="block rounded-lg shadow-lg bg-white border border-gray-300 w-full">
            <?php if (!empty($errors)) { ?>
                <div class="bg-red-300 p-5 rounded border border-red-800 text-red-800 my-4">
                    <?php foreach ($errors as $error) { ?>
                        <p><?= $error; ?></p>
                    <?php } ?>
                </div>
            <?php } ?>

                
            
            <div id="reviewP" class="py-3 px-6 border-b border-gray-300">
                Publier un avis :
            </div>
            <div class="p-6 text-center">
                <form action="" method="post" enctype="multipart/form-data">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                            Nom :
                        </label>
                        <?php if (empty($_SESSION['name'])) { ?>
                            <input  class="shadow appearance-none border rounded w-1/2 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" name="name" type="text" placeholder="Votre nom">
                        <?php } else { ?>
                            <input readonly="readonly" value="<?= $_SESSION['name'] ?>"  class="shadow appearance-none border rounded w-1/2 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" name="name" type="text" placeholder="Votre nom">
                        <?php } ?>
                        
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="review">
                            Commentaire :
                        </label>
                        <textarea class="shadow appearance-none border rounded w-1/2 py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="review" name="review" placeholder="Votre commentaire"></textarea>
                    </div>

                    <div class="mb-6">
                        
                        <label for="file" class="block text-gray-700 text-sm font-bold mb-2">Image :</label>
                        <input class="w-1/2 file:bg-blue-200 file:border-0 file:rounded-lg file:duration-500 hover:file:bg-blue-500 file:px-3 file:py-2 file:cursor-pointer" type="file" name="file" id="file">
                        
                    </div>

                    <div class="mb-6">
                            <label class="mr-2">Note :</label>
                            <?php for ($i=1; $i <= 5; $i++) { ?>
                                <input type="radio" name="note" id="<?= $i ?>" class="rounded" value="<?= $i ?>">
                                <label class="mr-2" for="<?= $i ?>"><?= $i ?></label>
                           <?php } ?>
                    </div>
                   
                    <button  class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">Noter</button>
                </form>

            </div>
           
        </div>
    </div>
    <?php } ?>

    <!-- Affichage des commentaires -->

        <div class="flex flex-col-reverse w-4/6">
        <?php foreach ($notes as $note) { ?>

            <div class="flex justify-center w-full mt-5 mb-5">
                <div class="mr-3">
                    <div class="flex items-center justify-center text-4xl font-medium text-center rounded-full h-20 w-20 bg-amber-300 ">
                        <?= substr($note['name'], 0, 1) ?>
                    </div>
                </div>
                <div class="block rounded-lg shadow-lg bg-white border border-gray-300 w-full">
                    <div class="py-3 px-6 border-b border-gray-300">
                        <?= $note['name'] ?>
                    </div>

                    <div class="p-5">

                    <?php for ($i=1; $i < 6 ; $i++) { ?>
                        <?php if ($note['note'] >= $i) { ?>
                            <span class="text-amber-400">&#9733;</span> 
                       <?php } else { ?>
                        <span class="text-gray-400">&#9733;</span> 
                       <?php } ?> 
                   <?php } ?>
                        
                    <p><?= $note['review'] ?></p>
                    <?php if ($note['image']) { ?>
                        <img class="w-[100px]" src="uploads/<?= $note['image'] ?>" alt="">  
                  <?php  } ?>
                        
                    </div>
        
                    <div class="py-3 px-6 border-t border-gray-300 text-end">
                        <?= $note['created_at'] ?>
                    </div>
                </div>
            </div>

        <?php } ?>
        </div>
   

    



</body>
</html>