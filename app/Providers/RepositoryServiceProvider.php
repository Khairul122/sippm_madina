<?php

namespace App\Providers;

use App\Domain\Activity\Repositories\ActivityRepositoryInterface;
use App\Domain\Complaint\Repositories\ComplaintRepositoryInterface;
use App\Domain\Complaint\Repositories\ComplaintStatusHistoryRepositoryInterface;
use App\Domain\Complaint\Repositories\DispositionRepositoryInterface;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentActivityRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentComplaintRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentComplaintStatusHistoryRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentDispositionRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentNotificationRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Binds every Domain repository interface to its Eloquent implementation.
 * This is the concrete mechanism for the "outer depends on inner" Clean
 * Architecture rule in this Laravel app: Application-layer UseCases
 * (Fase 4) type-hint the Domain interfaces, and Laravel's container
 * resolves them to these Infrastructure implementations via this binding.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ComplaintRepositoryInterface::class, EloquentComplaintRepository::class);
        $this->app->bind(ComplaintStatusHistoryRepositoryInterface::class, EloquentComplaintStatusHistoryRepository::class);
        $this->app->bind(DispositionRepositoryInterface::class, EloquentDispositionRepository::class);
        $this->app->bind(ActivityRepositoryInterface::class, EloquentActivityRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, EloquentNotificationRepository::class);
    }
}
