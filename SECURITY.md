# Security Policy

## Project Scope

This project is an **internal Laravel backend API** created for **testing, staging, and frontend integration purposes only**.

- The source code is **not intended for public reuse**
- External users are **not permitted to clone, fork, or deploy this project**
- The repository is provided **only for code review and reference on GitHub**

Access to the repository is limited and controlled by the project owner.

---

## Supported Versions

Only the **currently deployed version** is supported with security updates.

| Version | Supported |
|--------|-----------|
| Current (main branch) | ✅ Yes |
| Older versions | ❌ No |

No security fixes will be provided for older or archived versions.

---

## Code Usage & Restrictions

- ❌ Cloning the repository is **not allowed**
- ❌ Forking or redistributing the code is **not allowed**
- ❌ Using the code in other projects is **not permitted**
- ✅ Code can be **viewed on GitHub only** for review or learning purposes

Any unauthorized use, redistribution, or deployment of this code is strictly prohibited.

---

## Reporting a Vulnerability

If you discover a security vulnerability in this project, please follow the steps below:

1. **Do NOT** create a public GitHub issue
2. Report the issue **privately** to the project maintainer
3. Include:
   - A clear description of the vulnerability
   - Steps to reproduce (if possible)
   - Potential impact

The maintainer will:
- Acknowledge the report within a reasonable time
- Investigate the issue
- Apply a fix if the vulnerability is valid

Invalid or non-reproducible reports may be declined.

---

## Security Notes

- Environment variables (`.env`) are not committed
- Secrets and keys are stored securely on the server
- Database seeders are not auto-executed on deployment
- Deployment is protected using secured GitHub webhooks

---

## Disclaimer

This project is provided **as-is** for internal use.  
The maintainers are not responsible for any misuse or unauthorized deployment of this code.
