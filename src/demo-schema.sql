CREATE EXTENSION IF NOT EXISTS postgis;

CREATE TABLE IF NOT EXISTS public.mdrrmo_classroom_incidents (
    id uuid PRIMARY KEY,
    incident_type text NOT NULL CHECK (incident_type IN
        ('Flood', 'Typhoon / severe weather', 'Fire', 'Earthquake', 'Landslide', 'Medical emergency', 'Other incident')),
    description text NOT NULL CHECK (char_length(description) BETWEEN 5 AND 500),
    persons_affected integer NOT NULL CHECK (persons_affected BETWEEN 0 AND 10000),
    location_name text NOT NULL,
    location geometry(Point, 4326) NOT NULL,
    status text NOT NULL DEFAULT 'submitted' CHECK (status IN ('submitted', 'reviewed', 'resolved')),
    created_at timestamptz NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS mdrrmo_classroom_incidents_recent
    ON public.mdrrmo_classroom_incidents (created_at DESC, id DESC);
