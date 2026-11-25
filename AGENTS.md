# AGENTS.md — bank.atlas.bk

**Read first.** The owner's rules for every project (mandatory checklists, synchronisation at the start and end of every session, standing rules) are in `AGENTS-master.md`, in the owner's private repository `agents-rules`, cloned at `~/agents-rules` on each of the owner's computers and loaded automatically by the global `~/.claude/CLAUDE.md`. Apply them before anything else. If they are not in your context, read `~/agents-rules/AGENTS-master.md` first; if you cannot, ask the owner before working. This file only holds what is specific to this project.

## This project

- **What it is**: Atlas Money, a mobile money web application inspired by Wave and Orange Money (accounts, transfers, withdrawals, administration). Almost the same code as `AtlasMoney`: ask the owner which one is current before working.
- **Category**: Personnel; public repository: nothing private may be written in it (no secret, no private matter).
- **Stack**: PHP 8, MySQL, HTML/CSS/JS, GitHub Actions (`bank.yml` on `main`).
- **Install, run, test**:
  - Local PHP and MySQL (for example XAMPP); import `atlasmoney.sql`; set the database connection in `db_connect.php` on this computer only (never commit real credentials).
- **Branches**: work and test on `stg`; `main` is production and only receives work the owner has validated, merged from `stg` on the owner's OK. A push to `main` deploys: the workflow `bank.yml` runs.
- **Folder**: mini-PC `D:\My Project\Personnel\bank.atlas.bk`; on another computer, `~/agents-rules/sync-all.cmd` clones it in the same place under that computer's root.
- **Where we are**: `README.md`; create `docs/agent/HANDOFF.md` (and the other traces of the master file) as soon as real work starts here.
- **Rules learned on this project**: none yet.
