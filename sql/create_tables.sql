-- ==============================
-- Bilet Satın Alma Platformu
-- Veritabanı Şeması (SQLite)
-- ==============================

PRAGMA foreign_keys = ON;

-- ==============================
-- Table: Bus_Company
-- ==============================
CREATE TABLE Bus_Company (
    id TEXT PRIMARY KEY,                    -- UUID
    name TEXT NOT NULL UNIQUE,              -- Firma adı (benzersiz)
    logo_path TEXT,                         -- Logo dosya yolu
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==============================
-- Table: User
-- ==============================
CREATE TABLE User (
    id TEXT PRIMARY KEY,                    -- UUID
    full_name TEXT NOT NULL,                -- Tam ad
    email TEXT NOT NULL UNIQUE,             -- Email (benzersiz)
    role TEXT NOT NULL CHECK(role IN ('user','company','admin')),  -- Kullanıcı rolü
    password TEXT NOT NULL,                 -- Hash'lenmiş şifre
    company_id TEXT NULL,                   -- Firma ID (company rolü için)
    balance INTEGER DEFAULT 800,            -- Sanal kredi bakiyesi
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES Bus_Company(id) ON DELETE SET NULL
);

-- ==============================
-- Table: Trips (Seferler)
-- ==============================
CREATE TABLE Trips (
    id TEXT PRIMARY KEY,                    -- UUID
    company_id TEXT NOT NULL,               -- Hangi firmaya ait
    departure_city TEXT NOT NULL,           -- Kalkış şehri
    destination_city TEXT NOT NULL,         -- Varış şehri
    departure_time DATETIME NOT NULL,       -- Kalkış tarihi/saati
    arrival_time DATETIME NOT NULL,         -- Varış tarihi/saati
    price INTEGER NOT NULL,                 -- Bilet fiyatı
    capacity INTEGER NOT NULL,              -- Toplam koltuk sayısı
    status TEXT DEFAULT 'active' CHECK(status IN ('active','canceled')),  -- Sefer durumu
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES Bus_Company(id) ON DELETE CASCADE
);

-- ==============================
-- Table: Tickets (Biletler)
-- ==============================
CREATE TABLE Tickets (
    id TEXT PRIMARY KEY,                    -- UUID
    trip_id TEXT NOT NULL,                  -- Hangi sefer
    user_id TEXT NOT NULL,                  -- Hangi kullanıcı
    status TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active','canceled','expired')),
    total_price INTEGER NOT NULL,           -- Ödenen tutar (kupon uygulandıysa indirimli)
    canceled_at TIMESTAMP NULL,             -- İptal tarihi (varsa)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES Trips(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE
);

-- ==============================
-- Table: Booked_Seats (Rezerve Koltuklar)
-- ==============================
CREATE TABLE Booked_Seats (
    id TEXT PRIMARY KEY,                    -- UUID
    ticket_id TEXT NOT NULL,                -- Hangi bilet
    seat_number INTEGER NOT NULL,           -- Koltuk numarası
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES Tickets(id) ON DELETE CASCADE,
    UNIQUE(ticket_id, seat_number)          -- Aynı bilette aynı koltuk tekrar edilemesin
);

-- ==============================
-- Table: Coupons (İndirim Kuponları)
-- ==============================
CREATE TABLE Coupons (
    id TEXT PRIMARY KEY,                    -- UUID
    code TEXT NOT NULL UNIQUE,              -- Kupon kodu (benzersiz)
    discount REAL NOT NULL CHECK(discount > 0 AND discount <= 100),  -- İndirim oranı (%)
    company_id TEXT NULL,                   -- NULL ise global (admin), değilse firma kuponu
    usage_limit INTEGER NOT NULL,           -- Toplam kullanım limiti
    used_count INTEGER DEFAULT 0,           -- Şu ana kadar kaç kez kullanıldı
    expire_date DATETIME NOT NULL,          -- Son kullanma tarihi
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES Bus_Company(id) ON DELETE CASCADE
);

-- ==============================
-- Table: User_Coupons (Kupon Kullanım Geçmişi)
-- ==============================
CREATE TABLE User_Coupons (
    id TEXT PRIMARY KEY,                    -- UUID
    coupon_id TEXT NOT NULL,                -- Hangi kupon
    user_id TEXT NOT NULL,                  -- Hangi kullanıcı
    ticket_id TEXT NULL,                    -- Hangi bilette kullanıldı
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coupon_id) REFERENCES Coupons(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_id) REFERENCES Tickets(id) ON DELETE SET NULL
);

-- ==============================
-- Indexes (Performans için)
-- ==============================

-- Sık yapılan aramalar için
CREATE INDEX idx_trips_departure ON Trips(departure_city, departure_time);
CREATE INDEX idx_trips_company ON Trips(company_id);
CREATE INDEX idx_tickets_user ON Tickets(user_id);
CREATE INDEX idx_tickets_trip ON Tickets(trip_id);
CREATE INDEX idx_booked_seats_ticket ON Booked_Seats(ticket_id);
CREATE INDEX idx_user_email ON User(email);
CREATE INDEX idx_coupons_code ON Coupons(code);
CREATE INDEX idx_user_coupons_user ON User_Coupons(user_id, coupon_id);
