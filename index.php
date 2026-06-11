<?php
session_start();
require_once 'config/database.php';

$poruke = [];
$korisnici = [];

// Dohvaćanje podataka isključivo za administratora
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    try {
        $stmt_poruke = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
        $poruke = $stmt_poruke->fetchAll();

        // Dohvaćamo sve korisnike osim trenutno ulogiranog admina
        $stmt_korisnici = $pdo->prepare("SELECT id, username, email, role, created_at FROM users WHERE id != ? ORDER BY created_at DESC");
        $stmt_korisnici->execute([$_SESSION['user_id']]);
        $korisnici = $stmt_korisnici->fetchAll();
    } catch (PDOException $e) {
        die("Greška pri dohvaćanju admin podataka: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Početna - Video Game Tracker</title>
    <link rel="stylesheet" href="public/css/style.css?v=2">
</head>
<body>

    <div class="nav-bar">
        <div><strong>Video Game Tracker</strong></div>
        <div>
            <a href="index.php">Početna</a>
            <a href="search.php">Tražilica</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="kolekcija.php">Moja Kolekcija</a>
                <a href="contact.php">Kontakt</a>
                <a href="logout.php" style="color: #ff5252;">Odjavi se</a>
            <?php else: ?>
                <a href="contact.php">Kontakt</a>
                <a href="login.php">Prijava</a>
                <a href="register.php">Registracija</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="dashboard-container">
        
        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] === 'user_deleted'): ?>
                <div class="msg success" style="display: block; margin-bottom: 20px;">
                    Korisnik je uspješno obrisan iz sustava.
                </div>
            <?php elseif($_GET['msg'] === 'logged_out'): ?>
                <div class="msg success" style="display: block; margin-bottom: 20px;">
                    Uspješno ste se odjavili. Do idućeg igranja!
                </div>
            <?php elseif($_GET['msg'] === 'message_deleted'): ?>
                <div class="msg success" style="display: block; margin-bottom: 20px;">
                    Poruka je uspješno označena kao riješena i uklonjena.
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <h1>Dobrodošao, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p>Tvoja uloga u sustavu je: <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong></p>

            <div class="action-buttons">
                <a href="search.php" class="btn-action btn-search">🔍 Pretraži i dodaj igre</a>
                <a href="kolekcija.php" class="btn-action btn-collection">🎮 Moja Kolekcija</a>
            </div>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <div class="admin-panel" style="max-width: 800px; text-align: left;">

                    <h3 style="text-align: center;">🛠️ Admin Kontrole: Pristigle poruke</h3>
                    <?php if (empty($poruke)): ?>
                        <p style="text-align: center; color: #aaa;">Trenutno nema novih poruka.</p>
                    <?php else: ?>
                        <?php foreach ($poruke as $p): ?>
                            <?php
                            $poruka_text = htmlspecialchars($p['message']);
                            $is_long = mb_strlen($poruka_text) > 120;
                            $preview = $is_long ? mb_substr($poruka_text, 0, 120) . '...' : $poruka_text;
                            ?>
                            <div style="background: #1e1e1e; padding: 15px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid #bb86fc;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; color: #aaa;">
                                    <strong>Od: <?php echo htmlspecialchars($p['name']); ?> (<?php echo htmlspecialchars($p['email']); ?>)</strong>
                                    <span><?php echo date('d.m.Y. H:i', strtotime($p['created_at'])); ?></span>
                                </div>
                                <h4 style="margin: 0 0 10px 0; color: #e0e0e0;"><?php echo htmlspecialchars($p['subject']); ?></h4>

                                <div class="message-body">
                                    <p class="preview-text" style="margin: 0; line-height: 1.5; color: #ddd;"><?php echo nl2br($preview); ?></p>
                                    <?php if ($is_long): ?>
                                        <p class="full-text" style="margin: 0; line-height: 1.5; color: #ddd; display: none;"><?php echo nl2br($poruka_text); ?></p>
                                        <button type="button" onclick="toggleMessage(this)" style="background: none; border: none; color: #03dac6; padding: 8px 0 0 0; font-size: 13px; font-weight: bold; cursor: pointer;">Prikaži više ⬇</button>
                                    <?php endif; ?>
                                </div>

                                <div style="display: flex; gap: 10px; margin-top: 15px;">
                                    <a href="mailto:<?php echo htmlspecialchars($p['email']); ?>" class="btn-action btn-search" style="padding: 8px; flex: 1; text-align: center; font-size: 14px;">Odgovori</a>
                                    <form action="delete_message.php" method="POST" style="margin: 0; flex: 1;">
                                        <input type="hidden" name="message_id" value="<?php echo $p['id']; ?>">
                                        <button type="submit" class="btn-danger" style="padding: 8px; margin: 0; width: 100%; background-color: #cf6679; color: #121212;" onclick="return confirm('Obrisati ovu poruku?');">Obriši / Riješeno</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <hr style="border-top: 1px solid #444; margin: 40px 0;">
                    
                    <h3 style="text-align: center;">👥 Upravljanje korisnicima</h3>
                    <?php if (empty($korisnici)): ?>
                        <p style="text-align: center; color: #aaa;">Nema drugih korisnika u sustavu.</p>
                    <?php else: ?>
                        <div>
                            <table class="responsive-table" style="width: 100%; border-collapse: collapse; margin-top: 20px; background: #1e1e1e; border-radius: 8px; overflow: hidden;">
                                <thead>
                                    <tr style="background: #2a2a2a; color: #bb86fc; text-align: left;">
                                        <th style="padding: 12px;">ID</th>
                                        <th style="padding: 12px;">Korisničko ime</th>
                                        <th style="padding: 12px;">E-mail</th>
                                        <th style="padding: 12px;">Uloga</th>
                                        <th style="padding: 12px;">Akcija</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($korisnici as $k): ?>
                                        <tr style="border-top: 1px solid #333;">
                                            <td data-label="ID" style="padding: 12px;"><?php echo $k['id']; ?></td>
                                            <td data-label="Korisničko ime" style="padding: 12px;"><strong><?php echo htmlspecialchars($k['username']); ?></strong></td>
                                            <td data-label="E-mail" style="padding: 12px; word-break: break-all;"><?php echo htmlspecialchars($k['email']); ?></td>
                                            <td data-label="Uloga" style="padding: 12px;"><?php echo htmlspecialchars($k['role']); ?></td>
                                            <td data-label="Akcija" style="padding: 12px;">
                                                <form action="delete_user.php" method="POST" style="margin: 0;">
                                                    <input type="hidden" name="user_id" value="<?php echo $k['id']; ?>">
                                                    <button type="submit" class="btn-danger" style="padding: 6px 12px; width: auto; font-size: 13px;" onclick="return confirm('Jeste li sigurni da želite obrisati ovog korisnika i sve njegove spremljene igre?');">Obriši</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        <?php else: ?>
            <h1>Dobrodošli u Video Game Tracker</h1>
            <p style="font-size: 18px; color: #aaa; margin-top: 20px;">
                Istražite enciklopediju igara, pratite one koje igrate i ocijenite naslove koje ste završili.
            </p>

            <div class="action-buttons">
                <a href="search.php" class="btn-action" style="background-color: #03dac6; color: #121212;">🔍 Istraži igre</a>
                <a href="login.php" class="btn-action" style="background-color: #bb86fc; color: #121212;">Prijavi se</a>
                <a href="register.php" class="btn-action" style="background-color: #7b1fa2; color: #ffffff;">Napravi račun</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="public/js/script.js"></script>
</body>
</html>