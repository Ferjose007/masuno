<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Producto;

class ProductController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();
        $productos = Producto::all();
        $this->view('admin/products/index', compact('productos'));
    }

    public function store()
    {
        $this->authorizeAdmin();
        if (!empty($_POST['nombre']) && isset($_POST['precio'])) {
            Producto::create($_POST);
        }
        header('Location: ' . BASE_URL . '/index.php?url=Product/index');
        exit;
    }

    public function update()
    {
        $this->authorizeAdmin();
        $id = $_POST['id'] ?? null;
        $prod = Producto::find((int)$id);
        
        if ($prod) {
            $prod->update($_POST);
        }
        header('Location: ' . BASE_URL . '/index.php?url=Product/index');
        exit;
    }

    public function delete()
    {
        $this->authorizeAdmin();
        $id = $_GET['id'] ?? null;
        $prod = Producto::find((int)$id);
        if ($prod) $prod->delete();
        header('Location: ' . BASE_URL . '/index.php?url=Product/index');
        exit;
    }

    public function toggle()
    {
        $this->authorizeAdmin();
        $id = $_GET['id'] ?? null;
        $prod = Producto::find((int)$id);
        if ($prod) $prod->toggleStatus();
        header('Location: ' . BASE_URL . '/index.php?url=Product/index');
        exit;
    }
}