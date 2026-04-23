<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\AppService\ProfitLossService;
use Illuminate\Console\Command;

class RecalculateOrderProfitLoss extends Command
{
    protected $signature = 'profit-loss:recalculate-orders {--order_id=} {--status=}';

    protected $description = 'Recalculate stored profit/loss snapshots for existing orders';

    public function __construct(
        private readonly ProfitLossService $profitLossService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->profitLossService->supportsOrderProfitColumns()) {
            $this->error('Profit/loss columns are not available yet. Run migrations first.');

            return self::FAILURE;
        }

        $query = Order::query()->select('id');

        if ($orderId = $this->option('order_id')) {
            $query->where('id', $orderId);
        }

        if ($status = $this->option('status')) {
            $query->where('order_status', $status);
        }

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->warn('No matching orders found.');

            return self::SUCCESS;
        }

        $this->info("Recalculating profit/loss for {$total} orders...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->orderBy('id')->chunkById(100, function ($orders) use ($bar) {
            foreach ($orders as $order) {
                $this->profitLossService->recalculateOrder($order->id);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info('Profit/loss recalculation completed.');

        return self::SUCCESS;
    }
}
