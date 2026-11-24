<?php
/**
 * ============================================================================
 * FILE: logout.php
 * FUNGSI: Menghapus sesi login pengguna dan mengarahkan kembali ke login.php.
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

logout_user();
session_start();
set_flash('info', 'Anda telah berhasil keluar dari sistem.');
redirect('login.php');
