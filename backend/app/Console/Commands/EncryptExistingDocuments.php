<?php

namespace App\Console\Commands;

use App\Models\Patient;
use App\Services\Security\FileEncryptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class EncryptExistingDocuments extends Command
{
    protected $signature = 'documents:encrypt';

    protected $description = 'Encrypt existing unencrypted patient documents';

    public function __construct(
        private readonly FileEncryptionService $encryptionService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting document encryption process...');

        $patients = Patient::whereRaw("document_photo_path NOT LIKE '%.encrypted'")->get();

        if ($patients->isEmpty()) {
            $this->info('No unencrypted documents found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$patients->count()} documents to encrypt.");

        $successCount = 0;
        $failCount = 0;

        foreach ($patients as $patient) {
            try {
                $oldPath = $patient->document_photo_path;

                if (Storage::disk('public')->exists($oldPath)) {
                    $fileContents = Storage::disk('public')->get($oldPath);
                } elseif (Storage::disk('local')->exists($oldPath)) {
                    $fileContents = Storage::disk('local')->get($oldPath);
                } else {
                    $this->error("File not found for patient {$patient->id}: {$oldPath}");
                    $failCount++;
                    continue;
                }

                $newPath = $this->encryptionService->encryptAndStore(
                    $fileContents,
                    'documents',
                    'jpg'
                );

                $patient->document_photo_path = $newPath;
                $patient->save();

                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
                if (Storage::disk('local')->exists($oldPath)) {
                    Storage::disk('local')->delete($oldPath);
                }

                $this->line("✓ Encrypted document for patient: {$patient->full_name}");
                $successCount++;
            } catch (\Exception $e) {
                $this->error("✗ Failed for patient {$patient->full_name}: {$e->getMessage()}");
                $failCount++;
            }
        }

        $this->newLine();
        $this->info("Encryption complete!");
        $this->info("Success: {$successCount} | Failed: {$failCount}");

        return Command::SUCCESS;
    }
}
