-- ============================================================
-- Gestion Informatisée d'une Clinique - Oracle
-- Script DDL/DML pour application PHP
-- ============================================================

-- TABLE SERVICE
CREATE TABLE SERVICE (
    code_service    VARCHAR2(10)   CONSTRAINT pk_service PRIMARY KEY,
    libelle_service VARCHAR2(100)  NOT NULL
);

-- TABLE MEDECIN
CREATE TABLE MEDECIN (
    code_medecin  VARCHAR2(10)   CONSTRAINT pk_medecin PRIMARY KEY,
    nom           VARCHAR2(50)   NOT NULL,
    prenom        VARCHAR2(50)   NOT NULL,
    specialite    VARCHAR2(100),
    code_service  VARCHAR2(10)
        CONSTRAINT fk_med_service REFERENCES SERVICE(code_service)
);

-- TABLE PATIENT
CREATE TABLE PATIENT (
    code_patient   VARCHAR2(10)   CONSTRAINT pk_patient PRIMARY KEY,
    nom            VARCHAR2(50)   NOT NULL,
    prenom         VARCHAR2(50)   NOT NULL,
    date_naissance DATE,
    sexe           CHAR(1)        CHECK (sexe IN ('M','F')),
    adresse        VARCHAR2(200),
    telephone      VARCHAR2(20)
);

-- SEQUENCE CONSULTATION
CREATE SEQUENCE seq_consultation START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;

-- TABLE CONSULTATION
CREATE TABLE CONSULTATION (
    num_consultation NUMBER         CONSTRAINT pk_consult PRIMARY KEY,
    date_consult     DATE           NOT NULL,
    diagnostic       VARCHAR2(500),
    code_patient     VARCHAR2(10)
        CONSTRAINT fk_consult_patient REFERENCES PATIENT(code_patient),
    code_medecin     VARCHAR2(10)
        CONSTRAINT fk_consult_medecin REFERENCES MEDECIN(code_medecin),
    montant_total    NUMBER(10,2)   DEFAULT 0
);

-- TABLE TRAITEMENT
CREATE TABLE TRAITEMENT (
    code_traitement VARCHAR2(10)   CONSTRAINT pk_traitement PRIMARY KEY,
    libelle         VARCHAR2(200)  NOT NULL,
    prix_unitaire   NUMBER(10,2)   NOT NULL CHECK (prix_unitaire >= 0)
);

-- TABLE ASSOCIATION
CREATE TABLE CONSULTATION_TRAITEMENT (
    num_consultation NUMBER
        CONSTRAINT fk_ct_consult REFERENCES CONSULTATION(num_consultation) ON DELETE CASCADE,
    code_traitement  VARCHAR2(10)
        CONSTRAINT fk_ct_traitement REFERENCES TRAITEMENT(code_traitement),
    quantite         NUMBER  DEFAULT 1 CHECK (quantite > 0),
    CONSTRAINT pk_ct PRIMARY KEY (num_consultation, code_traitement)
);

-- AUDIT
CREATE TABLE JOURNAL_CONSULTATION (
    id_journal       NUMBER GENERATED ALWAYS AS IDENTITY,
    num_consultation NUMBER,
    action           VARCHAR2(10),
    date_action      DATE DEFAULT SYSDATE,
    utilisateur      VARCHAR2(50) DEFAULT USER,
    CONSTRAINT pk_journal PRIMARY KEY (id_journal)
);

-- TABLE AGENT (authentification application)
CREATE TABLE AGENT (
    id_agent          NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    login             VARCHAR2(50) UNIQUE NOT NULL,
    mot_de_passe_hash VARCHAR2(255) NOT NULL,
    nom_complet       VARCHAR2(120) NOT NULL,
    actif             CHAR(1) DEFAULT 'O' CHECK (actif IN ('O','N'))
);

-- TRIGGER CALCUL MONTANT
CREATE OR REPLACE TRIGGER trg_calcul_montant
AFTER INSERT OR UPDATE OR DELETE ON CONSULTATION_TRAITEMENT
FOR EACH ROW
DECLARE
    v_num NUMBER;
    v_total NUMBER;
BEGIN
    IF DELETING THEN
        v_num := :OLD.num_consultation;
    ELSE
        v_num := :NEW.num_consultation;
    END IF;

    SELECT NVL(SUM(ct.quantite * t.prix_unitaire), 0)
      INTO v_total
      FROM CONSULTATION_TRAITEMENT ct
      JOIN TRAITEMENT t ON ct.code_traitement = t.code_traitement
     WHERE ct.num_consultation = v_num;

    UPDATE CONSULTATION
       SET montant_total = v_total
     WHERE num_consultation = v_num;
END;
/

-- TRIGGER JOURNAL CONSULTATION
CREATE OR REPLACE TRIGGER trg_journal_consultation
AFTER INSERT OR DELETE ON CONSULTATION
FOR EACH ROW
BEGIN
    INSERT INTO JOURNAL_CONSULTATION (num_consultation, action)
    VALUES (CASE WHEN INSERTING THEN :NEW.num_consultation ELSE :OLD.num_consultation END,
            CASE WHEN INSERTING THEN 'INSERT' ELSE 'DELETE' END);
END;
/

-- DONNEES DE BASE
INSERT INTO SERVICE VALUES ('SRV01', 'Cardiologie');
INSERT INTO SERVICE VALUES ('SRV02', 'Pédiatrie');
INSERT INTO SERVICE VALUES ('SRV03', 'Chirurgie');

INSERT INTO MEDECIN VALUES ('MED01','Kaboré','Moussa','Cardiologue','SRV01');
INSERT INTO MEDECIN VALUES ('MED02','Sawadogo','Fatoumata','Pédiatre','SRV02');
INSERT INTO MEDECIN VALUES ('MED03','Ouédraogo','Ibrahim','Chirurgien','SRV03');

INSERT INTO PATIENT VALUES ('PAT01','Compaoré','Alice',TO_DATE('1990-03-15','YYYY-MM-DD'),'F','Secteur 12 Ouagadougou','70100001');
INSERT INTO PATIENT VALUES ('PAT02','Diallo','Boubacar',TO_DATE('1975-07-22','YYYY-MM-DD'),'M','Secteur 5 Ouagadougou','70100002');

INSERT INTO TRAITEMENT VALUES ('TRT01','Consultation de base',5000);
INSERT INTO TRAITEMENT VALUES ('TRT03','Analyse sanguine',8000);
INSERT INTO TRAITEMENT VALUES ('TRT06','Amoxicilline 1g (boite)',4500);

-- consultations dont certaines dans les dernières 24h
INSERT INTO CONSULTATION (num_consultation, date_consult, diagnostic, code_patient, code_medecin)
VALUES (seq_consultation.NEXTVAL, SYSDATE - 2, 'Contrôle général', 'PAT01', 'MED01');

INSERT INTO CONSULTATION (num_consultation, date_consult, diagnostic, code_patient, code_medecin)
VALUES (seq_consultation.NEXTVAL, SYSDATE - 0.2, 'Fièvre', 'PAT02', 'MED02');

INSERT INTO CONSULTATION_TRAITEMENT VALUES (1,'TRT01',1);
INSERT INTO CONSULTATION_TRAITEMENT VALUES (2,'TRT01',1);
INSERT INTO CONSULTATION_TRAITEMENT VALUES (2,'TRT03',1);
INSERT INTO CONSULTATION_TRAITEMENT VALUES (2,'TRT06',1);

-- agent de connexion: login=agent1 / mot de passe=agent123
INSERT INTO AGENT(login, mot_de_passe_hash, nom_complet, actif)
VALUES ('agent1', '$2y$12$mJV2RRdLia8YRpaRlKcLW.gSY9h85mS5SbaHEyLmizXbu4nnPQWiC', 'Agent Démonstration', 'O');

COMMIT;
