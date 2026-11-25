<?php

namespace App\Controllers;

use App\Libraries\DataStore;
use CodeIgniter\Exceptions\PageNotFoundException;


class Files extends BaseController
{
    public function document(string $id)
    {
        $doc = DataStore::find('documents', $id);
        if (! $doc || empty($doc['file_path'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . 'uploads/documents/' . basename($doc['file_path']);
        if (! is_file($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->download($path, null)
            ->setFileName($doc['file_name'] ?: basename($path));
    }

    /**
     * Resident photo. Staff can see any photo; a resident account can only
     * see its own.
     */
    public function resident(string $id)
    {
        $isResident = strcasecmp((string) session()->get('role'), 'Resident') === 0;
        if ($isResident && (string) session()->get('residentId') !== $id) {
            throw PageNotFoundException::forPageNotFound();
        }

        $row = DataStore::find('residents', $id);
        if (! $row || empty($row['photo'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . 'uploads/residents/' . basename($row['photo']);
        if (! is_file($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', 'image/jpeg')
            ->setHeader('Cache-Control', 'private, max-age=86400')
            ->setBody(file_get_contents($path));
    }
}
