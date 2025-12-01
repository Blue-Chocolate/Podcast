# Haqqeq (حقق) Client

A modern web platform for the Haqqeq podcast, featuring blogs, episodes, podcasts, and book releases From Haqqeq.

## 🚀 Tech Stack & Libraries

- **Framework:** React with TypeScript
- **Build Tool:** Vite
- **Styling:** Tailwind CSS + shadcn/ui
- **Data Fetching:** TanStack Query & Axios
- **Audio Processing:** wavesurfer.js
- **Form Handling:** React Form Hook
- **Validation:** Zod

## 📁 Project Structure

```
haqqeq-client/
├── public/
├── src/
│   ├── assets/
│   ├── components/
│   │   ├── cards/
│   │   ├── sections/
│   │   └── ui/
│   ├── layouts/ (Wrappers, interface layouts and Guards)
│   ├── pages/
│   ├── services/ (API Requests)
│   ├── lib/ (Utilities)
│   ├── hooks/ (queries & utility hooks)
│   ├── constants/
│   ├── context/
│   └── schemas/  (types & validation)
```

## 🛠️ Setup & Development

1. **Clone the repository**

```bash
git clone https://github.com/your-username/haqqeq-client.git
cd haqqeq-client
```

2. **Install dependencies**

```bash
npm install
```

3. **Environment Setup**
   Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Configure your environment variables:

```env
VITE_API_BASE_URL=http://localhost:3000
```

4. **Start development server**

```bash
npm run dev
```

## 📑 Features

- **Home Page**: Featured content and latest updates
- **Podcasts**: Audio episodes with waveform visualization
- **Blogs**: Written content and articles
- **Episodes**: Categorized podcast episodes
- **Book Releases**: Latest book releases and updates

## 📦 Building for Production

```bash
npm run build
```

The built files will be in the `dist` directory.

## ⚙️ Helper NPM scripts

The project exposes a few convenience scripts in package.json. Use these during development and CI:

- npm run dev  
  Start the Vite development server (hot reload).

- npm run build  
  Run TypeScript build (tsc -b) then produce a production build with Vite. Produces the dist/ folder.

- npm run preview  
  Serve the production build locally using Vite preview (useful to check the built output).

- npm run lint  
  Run ESLint for the repo.

- npm run format  
  Format files with Prettier and auto-fix lintable issues (Prettier + eslint --fix).

- npm run check  
  Run full checks: TypeScript build, ESLint, and Prettier check.
