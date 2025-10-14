<?php
/**
 * Sefer Detayları ve Koltuk Seçimi (Görünüm) - YENİ 2+1 DÜZEN
 */
?>
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Sefer Detayları</h2>
            <img src="<?= htmlspecialchars($trip['logo_path']) ?>" alt="<?= htmlspecialchars($trip['company_name']) ?>" class="company-logo-detail">
        </div>
        <div class="card-body">
            <h3><?= htmlspecialchars($trip['departure_city']) ?> → <?= htmlspecialchars($trip['destination_city']) ?></h3>
            <p><strong>Firma:</strong> <?= htmlspecialchars($trip['company_name']) ?></p>
            <p><strong>Kalkış:</strong> <?= date(DATETIME_FORMAT, strtotime($trip['departure_time'])) ?></p>
            <p><strong>Varış:</strong> <?= date(DATETIME_FORMAT, strtotime($trip['arrival_time'])) ?></p>
            <p class="price-info"><strong>Bilet Fiyatı:</strong> <?= number_format($trip['price'], 0) ?> ₺</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Koltuk Seçimi</h3>
        </div>
        <div class="card-body">
            <form id="booking-form" action="/checkout" method="POST">
                <?= CSRF::getTokenField() ?>
                <input type="hidden" name="trip_id" value="<?= htmlspecialchars($trip['id']) ?>">
                <input type="hidden" id="selected-seats-input" name="selected_seats" required>

                <div class="bus-layout">
                    <div class="bus-front">
                        <div class="driver-seat">Şoför</div>
                    </div>
                    <div class="seat-area">
                        <div class="seat-column-single">
                            <?php for ($i = 1; $i <= $trip['capacity']; $i++): ?>
                                <?php if ($i % 3 === 1 && $i < $trip['capacity'] - 4): // Sol Sütun (1, 4, 7...) ?>
                                    <?php
                                        $isBooked = in_array($i, $bookedSeats);
                                        $seatClass = $isBooked ? 'seat-booked' : 'seat-available';
                                    ?>
                                    <div class="seat <?= $seatClass ?>" data-seat-number="<?= $i ?>"><?= $i ?></div>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>

                        <div class="seat-column-double">
                            <?php for ($i = 1; $i <= $trip['capacity']; $i++): ?>
                                <?php if ($i % 3 !== 1 && $i < $trip['capacity'] - 4): // Sağ Sütun (2,3, 5,6...) ?>
                                    <?php
                                        $isBooked = in_array($i, $bookedSeats);
                                        $seatClass = $isBooked ? 'seat-booked' : 'seat-available';
                                    ?>
                                    <div class="seat <?= $seatClass ?>" data-seat-number="<?= $i ?>"><?= $i ?></div>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="seat-row-back">
                        <?php for ($i = $trip['capacity'] - 4; $i <= $trip['capacity']; $i++): // Son 5 koltuk ?>
                             <?php
                                $isBooked = in_array($i, $bookedSeats);
                                $seatClass = $isBooked ? 'seat-booked' : 'seat-available';
                            ?>
                            <div class="seat <?= $seatClass ?>" data-seat-number="<?= $i ?>"><?= $i ?></div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="legend mt-3">
                    <div class="legend-item"><span class="seat seat-available"></span> Boş</div>
                    <div class="legend-item"><span class="seat seat-selected"></span> Seçili</div>
                    <div class="legend-item"><span class="seat seat-booked"></span> Dolu</div>
                </div>

                <hr>
                
                <div class="booking-summary">
                    <h4>Seçilen Koltuklar: <span id="selected-seats-display">Yok</span></h4>
                    <h4>Toplam Tutar: <span id="total-price-display">0 ₺</span></h4>
                </div>

                <?php if ($auth->isLoggedIn()): ?>
                    <button type="submit" class="btn btn-success btn-lg mt-3">Ödeme Adımına Geç</button>
                <?php else: ?>
                    <p class="alert alert-warning mt-3">Bilet satın alabilmek için lütfen giriş yapın.</p>
                    <a href="/login" class="btn btn-primary">Giriş Yap</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<style>
    .company-logo-detail { max-height: 40px; }
    .price-info { font-size: 1.2rem; color: var(--primary-color); }
    .legend { display: flex; gap: 20px; }
    .legend-item { display: flex; align-items: center; gap: 8px; }
    .legend-item .seat { width: 20px; height: 20px; cursor: default; font-size: 0.7rem; }
    .booking-summary h4 { margin-bottom: 0.5rem; }
    .seat { width: 50px; height: 50px; display: flex; justify-content: center; align-items: center; border-radius: var(--radius-sm); border: 1px solid var(--gray-300); font-weight: 600; cursor: pointer; user-select: none; }
    .seat-available { background-color: #e0f2fe; }
    .seat-available:hover { background-color: #bae6fd; }
    .seat-booked { background-color: var(--gray-400); color: white; cursor: not-allowed; }
    .seat-selected { background-color: var(--success-color); color: white; border-color: #059669; }

    /* --- YENİ EKLENEN OTOBÜS CSS'İ --- */
    .bus-layout {
        border: 2px solid var(--gray-300);
        border-radius: 20px 20px 10px 10px;
        padding: 20px;
        background-color: var(--gray-50);
        max-width: 400px;
        margin: 0 auto;
    }
    .bus-front {
        margin-bottom: 20px;
        position: relative;
        height: 50px;
    }
    .driver-seat {
        position: absolute;
        left: 0;
        top: 0;
        width: 50px;
        height: 50px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%236b7280'%3E%3Cpath d='M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8zm0-14a6 6 0 1 0 6 6 6 6 0 0 0-6-6zm0 10a4 4 0 1 1 4-4 4 4 0 0 1-4 4z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: center;
        background-size: 80%;
        border: 2px solid var(--gray-400);
        border-radius: 5px;
        text-indent: -9999px;
    }
    .seat-area {
        display: flex;
        justify-content: space-between;
    }
    .seat-column-single, .seat-column-double {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .seat-column-double {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    .seat-row-back {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
        border-top: 1px dashed var(--gray-300);
        padding-top: 20px;
    }
</style>

<script>
// JavaScript kodunda herhangi bir değişiklik gerekmemektedir.
// Mevcut script'iniz bu yeni yapı ile uyumlu çalışacaktır.
document.addEventListener('DOMContentLoaded', function() {
    const seatMap = document.querySelector('.bus-layout'); // Ana kapsayıcıyı seç
    const selectedSeatsInput = document.getElementById('selected-seats-input');
    const selectedSeatsDisplay = document.getElementById('selected-seats-display');
    const totalPriceDisplay = document.getElementById('total-price-display');
    const ticketPrice = <?= (int)$trip['price'] ?>;
    let selectedSeats = [];

    seatMap.addEventListener('click', function(e) {
        const seat = e.target;
        if (seat.classList.contains('seat-available') || seat.classList.contains('seat-selected')) {
            seat.classList.toggle('seat-selected');
            const seatNumber = seat.dataset.seatNumber;
            
            if (seat.classList.contains('seat-selected')) {
                if (!selectedSeats.includes(seatNumber)) {
                    selectedSeats.push(seatNumber);
                }
            } else {
                selectedSeats = selectedSeats.filter(s => s !== seatNumber);
            }
            updateSummary();
        }
    });

    function updateSummary() {
        selectedSeats.sort((a, b) => parseInt(a) - parseInt(b));
        selectedSeatsInput.value = selectedSeats.join(',');

        if (selectedSeats.length > 0) {
            selectedSeatsDisplay.textContent = selectedSeats.join(', ');
            totalPriceDisplay.textContent = (selectedSeats.length * ticketPrice) + ' ₺';
        } else {
            selectedSeatsDisplay.textContent = 'Yok';
            totalPriceDisplay.textContent = '0 ₺';
        }
    }
});
</script>